<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use App\Service\IssueHandlerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DownloadController extends AbstractController
{

    /**
     * This function handles file downloads. Either PDF or DWG.
     * Also add the document to issues table.
     * @Route("/download/{documentId}", name="document.download", methods={"GET"})
     * @param int $documentId
     * @param Request $request
     * @param DocumentRepository $documentRepository
     * @param IssueHandlerService $issueHandlerService
     * @return Response
     */
    public function download(int $documentId, Request $request, DocumentRepository $documentRepository, IssueHandlerService $issueHandlerService)
    {
        $document = $documentRepository->find($documentId);

        if (!$document)
        {
            return $this->render("pages/blank.html.twig", ['message' => "Oeps, er ging iets fout. Dat document bestaat niet. Neem contact op met een administrator."]);
        }

        $requestType = $request->query->get("type");
        $issue = $request->query->get("issue");

        /** If the user is planning to return the document, we need to add it to the issue table */
        if ($issue == true)
        {
            /** If an issue already exists for this document, this is not possible. */
            if ($document->getIssue())
            {
                return $this->render("errors/error.html.twig", ['message' => "Dit document is al uitgegeven."]);
            }
            $issueHandlerService->addDocumentIssue($document, $this->getUser());
        }

        $basedir = $this->getParameter("app.path.documents");

        return $this->render("pages/download.html.twig", [
            "message" => "Uw download begint over enkele seconden...",
            "downloadLink" => $basedir . "/" . $document->getFileName()
        ]);
    }
}
