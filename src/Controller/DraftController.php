<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DocumentDraftRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DraftController extends AbstractController
{
    /**
     * @Route("/drafts", name="draft.index")
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
     */
    public function show()
    {

    }
}
