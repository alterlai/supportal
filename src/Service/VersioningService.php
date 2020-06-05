<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\DocumentDraft;
use App\Entity\DocumentHistory;
use App\Repository\DocumentDraftRepository;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VersioningService {

    private $entityManager;
    private $documentDraftRepository;
    private $documentNameParserService;
    private $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, DocumentDraftRepository $documentDraftRepository, DocumentNameParserService $documentNameParserService, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->documentDraftRepository = $documentDraftRepository;
        $this->documentNameParserService = $documentNameParserService;
        $this->parameterBag = $parameterBag;
    }

    public function archiveDocument(int $documentDraftId, UploadedFile $newPdfFile, string $directory)
    {
        $draft = $this->documentDraftRepository->find($documentDraftId);

        $currentDocument = $draft->getDocument();

        $this->createHistory($currentDocument);

        $this->saveNewPDF($newPdfFile, $currentDocument, $directory);

        $this->moveFromDraftToDocumentDirectory($draft);
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
     * @throws Exception
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

    private function moveFromDraftToDocumentDirectory(DocumentDraft $draft)
    {
        $draft_dir = $this->parameterBag->get('draft_upload_directory');
        $document_dir = $this->parameterBag->get('document_upload_directory');
        $currentDocument = $draft->getDocument();

        $newFileName = $this->documentNameParserService->generateFileNameFromEntities(
            $currentDocument->getBuilding(),
            $currentDocument->getDiscipline(),
            $currentDocument->getDocumentType(),
            $currentDocument->getFloor(),
            $currentDocument->getVersion() + 1, // +1 for the new revision
            ".dwg"
        );

        $filesystem = new Filesystem();
        try {
            $filesystem->rename($draft_dir.$draft->getFileName(), $document_dir.$newFileName);
        }
        catch (\Exception $e) {
            throw $e;
        }

    }
}