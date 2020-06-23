<?php

namespace App\Controller;

use App\Repository\DocumentHistoryRepository;
use App\Repository\DocumentRepository;
use App\Service\IssueHandlerService;
use App\Service\UserActionService;
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
     * Only the latest version of the specified document
     * @Route("/download/{documentId}", name="document.download", methods={"GET"})
     * @param int $documentId
     * @param Request $request
     * @param DocumentRepository $documentRepository
     * @param IssueHandlerService $issueHandlerService
     * @param UserActionService $userActionService
     * @return Response
     * @throws \Exception
     * @IsGranted("ROLE_USER")
     */
    public function download(int $documentId, Request $request, DocumentRepository $documentRepository, IssueHandlerService $issueHandlerService, UserActionService $userActionService)
    {
        $document = $documentRepository->find($documentId);
        $requestType = $request->query->get("type");
        $withIssue = $request->query->get("issue");
        $deadline = new \DateTimeImmutable("now +2 weeks");

        if (!$document) {
            $this->addFlash("danger", "Oeps, er ging iets fout. Dat document bestaat niet. Neem contact op met een administrator.");
            return $this->redirectToRoute("document", ['documentId' => $documentId]);
        }


        // Get correct file from request type
        switch (strtolower($requestType)) {
            case "dwg":
                $filename = $document->getFileName();
                $basedir = $this->getParameter("document_upload_directory");
                $userActionService->createUserAction($this->getUser(), $document, $deadline, "dwg", $withIssue);
                break;
            case "pdf":
                $filename = $document->getPdfFilename();
                $basedir = $this->getParameter("pdf_upload_directory");
                $userActionService->createUserAction($this->getUser(), $document, null, "pdf", $withIssue);
                break;
            default:
                return $this->render("errors/error.html.twig", ['message' => "Geen geldig bestandstype. Probeer het opnieuw."]);
        }

        /* Stop if the file doesn't exist. */
        if (!file_exists($basedir . $filename) || $filename == null) {
            $this->addFlash("danger", "The file you requested doesn't exist.");
            return $this->redirectToRoute("document", ['documentId' => $documentId]);
        }

        /** If the user is planning to return the document, we need to add it to the issue table */
        if ($withIssue) {
            // If the user doesn't have the correct permissions
            if (!$this->isGranted("ROLE_LEVERANCIER"))
                return $this->render("errors/error.html.twig", ['message', "Incorrect permissions"]);

            /** If an issue already exists for this document, this is not possible. */
            if ($document->getIssue()) {
                return $this->render("errors/error.html.twig", ['message' => "Dit document is al uitgegeven."]);
            }
            $issueHandlerService->addDocumentIssue($document, $this->getUser(), $deadline);
        }

        return $this->file($basedir . $filename);
    }


    /**
     * @Route("/download/{revisionId}/{fileType}", name="document.download.revision")
     * @param int $revisionId
     * @param string $fileType
     * @param DocumentHistoryRepository $documentHistoryRepository
     * @return Response
     * @throws \Exception
     */
    public function downloadRevision(int $revisionId, string $fileType, DocumentHistoryRepository $documentHistoryRepository, UserActionService $userActionService)
    {
        $documentVersion = $documentHistoryRepository->findOneBy(['id' => $revisionId]);

        if (!$documentVersion) {
            $this->addFlash("danger", "Oeps, er ging iets fout. Dat document bestaat niet. Neem contact op met een administrator.");
            return $this->redirectToRoute("document", ['documentId' => $documentVersion->getDocument()->getId()]);
        }

        // Get correct file from request type
        switch (strtolower($fileType)) {
            case "dwg":
                $filename = $documentVersion->getFileName();
                $basedir = $this->getParameter("document_upload_directory");
                $fileType = "dwg";
                break;
            case "pdf":
                $filename = $documentVersion->getPdfFilename();
                $basedir = $this->getParameter("pdf_upload_directory");
                $fileType = "pdf";
                break;
            default:
                $this->addFlash("danger", "Geen geldig bestandstype. Probeer het opnieuw.");
                return $this->redirectToRoute("document", ['documentId' => $documentVersion->getDocument()->getId()]);
        }

        /* Stop if the file doesn't exist. */
        if (!file_exists($basedir . $filename) || $filename == null) {
            $this->addFlash("danger", "The file you requested doesn't exist.");
            return $this->redirectToRoute("document", ['documentId' => $documentVersion->getDocument()->getId()]);
        }

        // Only add a user action if the file is available
        $userActionService->createUserAction($this->getUser(), $documentVersion->getDocument(), null, $fileType, false);

        return $this->file($basedir . $filename);
    }

}
