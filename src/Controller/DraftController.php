<?php

namespace App\Controller;

use App\Entity\DocumentHistory;
use App\Entity\User;
use App\Repository\DocumentDraftRepository;
use App\Repository\DocumentHistoryRepository;
use App\Repository\DraftStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param DraftStatusRepository $draftStatusRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function approve(int $id, DocumentDraftRepository $documentDraftRepository)
    {
        $draft = $documentDraftRepository->find($id);

        return $this->render('drafts/admin/show.html.twig', ['draft' => $draft]);
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
     * @param DocumentDraftRepository $documentDraftRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function accept(int $id, DocumentDraftRepository $documentDraftRepository, EntityManagerInterface $entityManager)
    {
        // Manier 1
        // 1. voeg de draft toe aan document revisions
        // move het bestand naar /archive
        // pas de bestandsnaam aan.
        // 2. Markeer de draft als geaccepteerd
        // 3. Mail de user

        // Manier 2 <--
        // 1. maak een record aan in document history met de huidige info.
        $draft = $documentDraftRepository->find($id);

        if (!$draft)
        {
            return $this->render('errors/error.html.twig', ['message' => "Draft ID is unknown."]);
        }
        // todo: fix updateBy
        $documentHistoryEntity = (new DocumentHistory())
            ->setDocument($draft->getDocument())
            ->setRevision($draft->getDocument()->getVersion())
            ->setRevisionDescription($draft->getDocument()->getDescription())
            ->setUpdatedAt($draft->getDocument()->getUpdatedAt())
//            ->setUpdatedBy($draft->getUploadedBy())
        ;

        $entityManager->persist($documentHistoryEntity);

        $entityManager->flush();

        // 2. verander het huidige record naar de juiste bestandsnaam



        // move het bestand naar /archive
        // pas de bestandsnamen aan.
        // 3. Markeer de draft als geaccepteerd
        // 4. Mail de user

        $this->addFlash("success", "Concept goedgekeurd. Het concept is verwerkt in de database.");

        return $this->render('drafts/admin/index.html.twig');

    }
}
