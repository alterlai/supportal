<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentHistory;
use App\Entity\User;
use App\Repository\DocumentDraftRepository;
use App\Repository\DraftStatusRepository;
use App\Service\DocumentNameParserService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class DraftController extends AbstractController
{
    /**
     * @Route("/drafts", name="draft.index")
     * @param DocumentDraftRepository $documentDraftRepository
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function index(DocumentDraftRepository $documentDraftRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $drafts = $documentDraftRepository->findBy(['uploaded_by' => $user->getId()]);

        return $this->render('drafts/index.html.twig', [
            'drafts' => $drafts,
        ]);
    }

    /**
     * @Route("/drafts/{id}", name="draft.show")
     * @param int $id
     * @param DocumentDraftRepository $documentDraftRepository
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function show(int $id, DocumentDraftRepository $documentDraftRepository)
    {
        $draft = $documentDraftRepository->find($id);
        return $this->render('drafts/show.html.twig', ['draft' => $draft]);
    }

    /**
     * Index all pending drafts
     * @Route("/admin/drafts", name="draft.check")
     * @param DocumentDraftRepository $documentDraftRepository
     * @param DraftStatusRepository $draftStatusRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function check(DocumentDraftRepository $documentDraftRepository, DraftStatusRepository $draftStatusRepository)
    {
        $waitingstatus = $draftStatusRepository->findOneBy(['name' => "In behandeling"]);
        $openDrafts = $documentDraftRepository->findBy(['draftStatus' => $waitingstatus]);
        return $this->render('drafts/admin/index.html.twig', ['drafts' => $openDrafts]);
    }

    /**
     * Show a draft
     * @Route("/admin/drafts/{id}", name="draft.approve")
     * @param int $id
     * @param DocumentDraftRepository $documentDraftRepository
     * @param FormBuilderInterface $formBuilder
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function approve(int $id, DocumentDraftRepository $documentDraftRepository)
    {
        $draft = $documentDraftRepository->find($id);

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('draft.accept', ['id' => $id]))
            ->setMethod("POST")
            ->add("pdfFile", FileType::class, [
                'label' => "PDF File",
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ]
            ])
            ->add("submit", SubmitType::class, [
                    'label' => "Accepteren",
                    'attr' => ["class" => "btn btn-success"]
        ])->getForm();

        return $this->render('drafts/admin/show.html.twig', [
            'draft' => $draft,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deny the draft and set the description for rejection
     * @Route("/admin/drafts/{id}/deny", name="draft.deny", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param DocumentDraftRepository $documentDraftRepository
     * @param DraftStatusRepository $draftStatusRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     * @throws \Exception
     */
    public function deny(int $id, Request $request, DocumentDraftRepository $documentDraftRepository, DraftStatusRepository $draftStatusRepository, EntityManagerInterface $entityManager)
    {
        //TODO mail versturen
        $deniedStatus = $draftStatusRepository->findOneBy(['name' => "Afgekeurd"]);
        $draft = $documentDraftRepository->find($id);
        $denyDescription = $request->query->get("denyDescription");

        $draft->setDraftStatus($deniedStatus);
        $draft->setRejectionDescription($denyDescription);
        $draft->setChangedAt(new \DateTime("now"));

        $entityManager->persist($draft);
        $entityManager->flush();

        $this->addFlash("success", "Concept afgekeurd.");
        return $this->render('drafts/admin/index.html.twig');
    }

    /**
     * @Route("/admin/drafts/{id}/accept", name="draft.accept", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @param DocumentDraftRepository $documentDraftRepository
     * @param EntityManagerInterface $entityManager
     * @param DocumentNameParserService $documentNameParserService
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function accept(int $id, Request $request, DocumentDraftRepository $documentDraftRepository, EntityManagerInterface $entityManager, DocumentNameParserService $documentNameParserService)
    {
        // 1. Make a new DocumentHistory object with current document values.
        $draft = $documentDraftRepository->find($id);

        $currentDocument = $draft->getDocument();

        // Error handling
        if (!$draft)
        {
            return $this->render('errors/error.html.twig', ['message' => "Draft ID is unknown."]);
        }
        // todo: fix updateBy
        $documentHistoryEntity = (new DocumentHistory())
            ->setDocument($currentDocument)
            ->setRevision($currentDocument->getVersion())
            ->setRevisionDescription($currentDocument->getDescription())
            ->setUpdatedAt($currentDocument->getUpdatedAt())
            ->setFileName($currentDocument->getFileName());
//            ->setUpdatedBy($draft->getUploadedBy())
        ;

        $entityManager->persist($documentHistoryEntity);

        $entityManager->flush();

        /** @var UploadedFile $pdfFile */
        $pdfFile = $request->files->get("form['pdfFile']");

        if (!$pdfFile)
        {
            return $this->render('errors/error.html.twig',
                ['message' => "PDF file cannot be empty"]);
        }

        try {
            $newPdfFileName = $this->saveNewPdfFileAndReturnFilename($documentNameParserService, $currentDocument, $pdfFile);
        }catch (FileException $e)
        {
            return $this->render('errors/error.html.twig',
                ['message' => "An error occured while moving the PDF file. Please try again or contact an administrator."]
            );
        }

        // move het bestand naar /archive
        // pas de bestandsnamen aan.
        // 3. Markeer de draft als geaccepteerd
        // 4. Mail de user

        $this->addFlash("success", "Concept goedgekeurd. Het concept is verwerkt in de database.");

        return $this->render('drafts/admin/index.html.twig');

    }

    /**
     * Save a PDF file on disk with new revision number.
     * @param DocumentNameParserService $documentNameParserService
     * @param Document $currentDocument
     * @param UploadedFile $pdfFile
     * @return string new file name
     */
    private function saveNewPdfFileAndReturnFilename(DocumentNameParserService $documentNameParserService, Document $currentDocument, UploadedFile $pdfFile)
    {

        $newFileName = $documentNameParserService->generateFileNameFromEntities(
            $currentDocument->getBuilding(),
            $currentDocument->getDiscipline(),
            $currentDocument->getDocumentType(),
            $currentDocument->getFloor(),
            $currentDocument->getVersion() + 1, // +1 for the new revision
            "pdf"
        );

        $pdfFile->move(
            $this->getParameter('app.path.documents'),
            $newFileName
        );

        return $newFileName;
    }
}
