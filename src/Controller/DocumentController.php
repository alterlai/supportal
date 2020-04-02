<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    /**
     * @Route("/document/", name="document", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function show(Request $request, DocumentRepository $documentRepository)
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
     * @Route("document/update/{documentId}", name="document.update", methods={"POST"})
     * @param int $documentId
     * @param Request $request
     * @return Response
     */
    public function updateDocument(int $documentId, Request $request)
    {
        // todo: logica voor uploaden bestand.
        return $this->render('pages/blank.html.twig', ['message' => "Het document is verstuurd voor goedkeuring."]);
    }
}
