<?php

namespace App\Controller;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @IsGranted("ROLE_USER")
     */
    public function show(Request $request, DocumentRepository $documentRepository)
    {
        if (!$id = $request->query->get("documentId"))
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
     * @Route("/document/update/{documentId}", name="document.update", methods={"POST"})
     * @param int $documentId
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function updateDocument(int $documentId, Request $request)
    {
        // todo: logica voor uploaden bestand.
        return $this->render('pages/blank.html.twig', ['message' => "Het document is verstuurd voor goedkeuring."]);
    }

    /**
     * @Route("/ajax/document", name="ajax.document", methods={"GET"})
     * @param Request $request
     * @param DocumentRepository $documentRepository
     * @return JsonResponse
     * @IsGranted("ROLE_USER")
     */
    public function ajax_filter_documents(Request $request, DocumentRepository $documentRepository)
    {
        // Deny non-ajax requests
        if (!$request->isXmlHttpRequest()) {
            return $this->render("pages/blank.html.twig", ['message' => "This page doesn't exist"]);
        }

        $disciplineGroups = $request->query->get("disciplineGroup");
        $
        $documents = $documentRepository->findAll();

        $jsonData = array();

        foreach ($documents as $document){
            array_push($jsonData, [
                'naam' => $document->getDocumentName(),
                'discipline' => $document->getDiscipline()->getCode(),
                'omschrijving' => $document->getDiscipline()->getDescription(),
                'gebouw' => $document->getBuilding(),
                'verdieping' => $document->getFloor(),
                'documentId' => $document->getId()
            ]);
        }
        return new JsonResponse($jsonData);
    }
}
