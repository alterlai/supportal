<?php

namespace App\Controller;

use App\Entity\Document;
use App\Repository\BuildingRepository;
use App\Repository\DisciplineRepository;
use App\Repository\DocumentRepository;
use App\Repository\DocumentTypeRepository;
use App\Service\DocumentFilterService;
use App\Service\IssueHandlerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentController extends AbstractController
{
    /**
     * @Route("/documents/{buildingId}", name="documents", methods={"GET"})
     * @param integer $buildingId
     * @param BuildingRepository $buildingRepository
     * @param DocumentTypeRepository $documentTypeRepository
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function index(int $buildingId, BuildingRepository $buildingRepository, DocumentTypeRepository $documentTypeRepository, DisciplineRepository $disciplineRepository)
    {
        $documents = ($buildingRepository->find($buildingId))->getDocuments();

        $documentTypes = $documentTypeRepository->findAllAsArray();

        $disciplines = $disciplineRepository->findAllAsGroupedArray();

        return $this->render('pages/documents.html.twig', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'buildingId' => $buildingId,
            'disciplineGroups' => $disciplines
        ]);
    }

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
     * @Route("/ajax/documents/{buildingId}", name="ajax.documents", methods={"GET"})
     * @param Request $request
     * @param int $buildingId
     * @param DocumentRepository $documentRepository
     * @return JsonResponse|Response
     * @IsGranted("ROLE_USER")
     */
    public function ajax_filter_documents(Request $request, int $buildingId, DocumentRepository $documentRepository)
    {
        // Deny non-ajax requests
        if (!$request->isXmlHttpRequest()) {
            return $this->render("pages/blank.html.twig", ['message' => "This page doesn't exist"]);
        }

        $disciplineGroups = $request->query->get("disciplineGroup");
        $documentTypes = $request->query->get("documentTypes");
        $floor = $request->query->get('floor');

        /** @var ArrayCollection|Document[] $documents */
        $documents = $documentRepository->findWithFilter($this->getUser(), $buildingId, $disciplineGroups, $floor, $documentTypes);

        $jsonData = array();

        foreach ($documents as $document){
            array_push($jsonData, [
                'naam' => $document->getDocumentName(),
                'discipline' => $document->getDiscipline()->getCode(),
                'omschrijving' => $document->getDiscipline()->getDescription(),
                'gebouw' => $document->getBuilding()->getName(),
                'verdieping' => $document->getFloor(),
                'documentId' => $document->getId(),
                'documentType' => $document->getDocumentType()->getName()
            ]);
        }
        return new JsonResponse($jsonData);
    }


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
            $issueHandlerService->addDocumentIssue($document, $this->getUser());
        }

        // Todo: Fetch the file and hand it over to the user.
        return $this->render("pages/blank.html.twig", ["message" => "Uw download begint over enkele seconden..."]);
    }

}
