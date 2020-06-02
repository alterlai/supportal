<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DocumentDraftRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/admin/drafts", name="draft.check")
     * @param DocumentDraftRepository $documentDraftRepository
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function check(DocumentDraftRepository $documentDraftRepository)
    {
        $openDrafts = $documentDraftRepository->findBy(['draftStatus' => '2']);
        return $this->render('drafts/admin/index.html.twig', ['drafts' => $openDrafts]);
    }

    /**
     * @Route("/admin/drafts/{id}", name="draft.approve")
     * @param int $id
     * @param DocumentDraftRepository $documentDraftRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function approve(int $id, DocumentDraftRepository $documentDraftRepository)
    {
        $draft = $documentDraftRepository->find($id);
        return $this->render('drafts/admin/show.html.twig', ['draft' => $draft]);
    }

    /**
     * @Route("/admin/drafts/{id}/deny", name="draft.deny", methods={"POST"})
     * @param int $id
     * @param DocumentDraftRepository $documentDraftRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function deny(int $id, DocumentDraftRepository $documentDraftRepository)
    {
        //todo
    }

    /**
     * @Route("/admin/drafts/{id}/accept", name="draft.accept", methods={"POST"})
     * @param int $id
     * @param DocumentDraftRepository $documentDraftRepository
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function accept(int $id, DocumentDraftRepository $documentDraftRepository)
    {
        //todo
    }
}
