<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use App\Service\IssueHandlerService;
use App\Service\UserActionService;
use DateTime;
use Monolog\DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_USER")
     * @throws \Exception
     */
    public function download(int $documentId, Request $request, DocumentRepository $documentRepository, IssueHandlerService $issueHandlerService, UserActionService $userActionService)
    {
        $document = $documentRepository->find($documentId);
        $basedir = $this->getParameter("app.path.documents");
        $requestType = $request->query->get("type");
        $issue = $request->query->get("issue");
        $deadline = new \DateTimeImmutable("now +2 weeks");

        if (!$document)
        {
            return $this->render("errors/error.html.twig", ['message' => "Oeps, er ging iets fout. Dat document bestaat niet. Neem contact op met een administrator."]);
        }


//        $viewHistory->

        /** If the user is planning to return the document, we need to add it to the issue table */
        if ($issue == true)
        {
            // If the user doesn't have the correct permissions
            if(!$this->isGranted("ROLE_LEVERANCIER"))
                return $this->render("errors/error.html.twig", ['message', "Incorrect permissions"]);

            /** If an issue already exists for this document, this is not possible. */
            if ($document->getIssue())
            {
                return $this->render("errors/error.html.twig", ['message' => "Dit document is al uitgegeven."]);
            }
            $issueHandlerService->addDocumentIssue($document, $this->getUser(), $deadline);
        }

        // Get correct file from request type
        switch (strtolower($requestType))
        {
            case "dwg":
                $filename = $document->getFileName();
                $userActionService->createUserAction($this->getUser(), $document, $deadline, "dwg");
                break;
            case "pdf":
                $filename = $document->getPdfFilename();
                $userActionService->createUserAction($this->getUser(), $document, null, "pdf");
                break;
            default:
                return $this->render("errors/error.html.twig", ['message' => "Geen geldig bestandstype. Probeer het opnieuw."]);
        }

        return $this->render("pages/download.html.twig", [
            "message" => "Uw download begint over enkele seconden...",
            "downloadLink" => $basedir . "/" . $filename
        ]);
    }
}
