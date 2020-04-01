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
     * @Route("/", name="home")
     * @IsGranted("ROLE_USER")
     */
    public function index(DocumentRepository $documentRepository)
    {
        $allDocuments = $documentRepository->findAll();
        return $this->render("pages/home.html.twig", ['documents' => $allDocuments]);
    }

    /**
     * @Route("/document/", name="document")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
   public function showDocument(Request $request, DocumentRepository $documentRepository)
   {
       if (!$id = $request->query->get("id"))
       {
           throw new \Exception("Invalid document");
       }
       $document = $documentRepository->find($id);
       if (!$document)
       {
           throw new \Exception("Unknown document number");
       }

       return $this->render('pages/document.html.twig', ['document' => $document]);
   }

    /**
     * @Route("/search/", name="search")
     * @return Response
     * @throws \Exception
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
