<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function index(DocumentRepository $documentRepository)
    {
        $documents = $documentRepository->findbyCurrentUser($this->getUser());
        return $this->render("pages/home.html.twig", ['documents' => $documents]);
    }

}
