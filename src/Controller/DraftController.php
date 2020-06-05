<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentHistory;
use App\Entity\User;
use App\Form\PDFUpload;
use App\Repository\DocumentDraftRepository;
use App\Repository\DraftStatusRepository;
use App\Service\DocumentNameParserService;
use App\Service\VersioningService;
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
     * Show a draft and handle the accept.
     * @Route("/admin/drafts/{documentDraftId}", name="draft.approve")
     * @param int $documentDraftId
     * @param Request $request
     * @param DocumentDraftRepository $documentDraftRepository
     * @param VersioningService $versioningService
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function approve(int $documentDraftId, Request $request, DocumentDraftRepository $documentDraftRepository, VersioningService $versioningService)
    {
        $draft = $documentDraftRepository->find($documentDraftId);

        // Error handling
        if (!$draft)
        {
            return $this->render('errors/error.html.twig', ['message' => "Draft ID is unknown."]);
        }

        $form = $this->createForm(PDFUpload::class);
        $form->handleRequest($request);

        // Handle submitted draft form when the user clicks accept.
        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $newPdfFile */
            $newPdfFile = $form->get('pdfFile')->getData();
            $directory = $this->getParameter('document_upload_directory');

            if (!$newPdfFile)
            {
                return $this->render('errors/error.html.twig',
                    ['message' => "PDF file cannot be empty"]);
            }

            $versioningService->archiveDocument($documentDraftId, $newPdfFile, $directory);

            $this->addFlash("success", "Concept goedgekeurd. Het concept is verwerkt in de database.");

            return $this->render('drafts/admin/index.html.twig');
        }

        return $this->render('drafts/admin/show.html.twig', [
            'draft' => $draft,
            'action' => $this->generateUrl('draft.approve', ['documentDraftId' => $draft->getId()]),
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
     * @param int $documentDraftId
     * @param mixed $data
     * @param DocumentNameParserService $documentNameParserService
     * @param VersioningService $versioningService
     * @return Response
     */
    private function accept(int $documentDraftId, $data, DocumentNameParserService $documentNameParserService, VersioningService $versioningService)
    {
        // 1. Make a new DocumentHistory object with current document values.
        
        // pas de bestandsnamen aan.
        // 3. Markeer de draft als geaccepteerd
        // 4. Mail de user

    }

}
