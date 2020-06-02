<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DocumentDraftRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DraftController extends AbstractController
{
    /**
     * @Route("/drafts", name="draft.index")
     * @param DocumentDraftRepository $documentDraftRepository
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function check(DocumentDraftRepository $documentDraftRepository)
    {
        $openDrafts = $documentDraftRepository->findAll();
        return $this->render('drafts/index_all.html.twig', ['drafts' => $openDrafts]);
    }
}
