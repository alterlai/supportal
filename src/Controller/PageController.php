<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(DocumentRepository $documentRepository)
    {
        $allDocuments = $documentRepository->findAll();
        return $this->render("pages/home.html.twig", ['documents' => $allDocuments]);
    }

    /**
     * @Route("/search/", name="search", methods={"GET"})
     * @return Response
     * @throws \Exception
     * @IsGranted("ROLE_USER")
     */
    public function searchDocuments(DocumentRepository $documentRepository, Request $request)
    {
        if (!$request->get("search"))
        {
            $documents = $documentRepository->findAll();
        }

        $documents = $documentRepository->searchAllColumns($request->get("search"));

        return $this->render("pages/documents.html.twig", ['documents' => $documents]);
    }
}
