<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\DocumentDraft;
use App\Entity\DocumentHistory;
use App\Repository\DocumentDraftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VersioningService {

    private $entityManager;
    private $documentDraftRepository;
    private $documentNameParserService;

    public function __construct(EntityManagerInterface $entityManager, DocumentDraftRepository $documentDraftRepository, DocumentNameParserService $documentNameParserService)
    {
        $this->entityManager = $entityManager;
        $this->documentDraftRepository = $documentDraftRepository;
        $this->documentNameParserService = $documentNameParserService;
    }

    public function archiveDocument(int $documentDraftId, UploadedFile $newPdfFile, string $directory)
    {
        $draft = $this->documentDraftRepository->find($documentDraftId);

        $currentDocument = $draft->getDocument();

        $this->createHistory($currentDocument);

        $this->saveNewPDF($newPdfFile, $currentDocument, $directory);
    }

    /**
     * Create a new documentHistory entity and save it to the database.
     * @param Document $currentDocument
     */
    private function createHistory(Document $currentDocument)
    {
        // todo: fix updateBy
        $documentHistoryEntity = (new DocumentHistory())
            ->setDocument($currentDocument)
            ->setRevision($currentDocument->getVersion())
            ->setRevisionDescription($currentDocument->getDescription())
            ->setUpdatedAt($currentDocument->getUpdatedAt())
            ->setFileName($currentDocument->getFileName());
//            ->setUpdatedBy($draft->getUploadedBy())
        ;

        $this->entityManager->persist($documentHistoryEntity);

        $this->entityManager->flush();
    }


    /**
     * Save the PDF file to disk in the correct directory.
     * @param UploadedFile $newPdfFile
     * @param Document $currentDocument
     * @param string $directory
     * @return string
     */
    private function saveNewPDF(UploadedFile $newPdfFile, Document $currentDocument, string $directory)
    {
        $newFileName = $this->documentNameParserService->generateFileNameFromEntities(
            $currentDocument->getBuilding(),
            $currentDocument->getDiscipline(),
            $currentDocument->getDocumentType(),
            $currentDocument->getFloor(),
            $currentDocument->getVersion() + 1, // +1 for the new revision
            ".pdf"
        );

        $newPdfFile->move($directory, $newFileName);

        return $newFileName;
    }
}