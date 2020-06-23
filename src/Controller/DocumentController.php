<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Repository\DisciplineRepository;
use App\Repository\DocumentDraftRepository;
use App\Repository\DocumentRepository;
use App\Repository\DocumentTypeRepository;
use App\Repository\LocationRepository;
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
     * @Route("documents/oud", name="document.oud")
     * @param LocationRepository $locationRepository
     * @param DocumentTypeRepository $documentTypeRepository
     * @param DisciplineRepository $disciplineRepository
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function index_oud(LocationRepository $locationRepository, DocumentTypeRepository $documentTypeRepository, DisciplineRepository $disciplineRepository, DocumentRepository $documentRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $locations = $locationRepository->findBy(['organisation' => $user->getOrganisation()]);

        $documents = $documentRepository->findByCurrentUser($user);

        $documentTypes = $documentTypeRepository->findAllAsArray();

        $disciplines = $disciplineRepository->findAllAsGroupedArray();

        return $this->render('pages/documents.html.twig', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'disciplineGroups' => $disciplines,
            'locations' => $locations
        ]);
    }

    /**
     * @Route("/documents/", name="documents.index")
     * @param LocationRepository $locationRepository
     * @param DocumentTypeRepository $documentTypeRepository
     * @param DisciplineRepository $disciplineRepository
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function index(LocationRepository $locationRepository, DocumentTypeRepository $documentTypeRepository, DisciplineRepository $disciplineRepository, DocumentRepository $documentRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $locations = $locationRepository->findBy(['organisation' => $user->getOrganisation()]);

        $documents = $documentRepository->findByCurrentUser($user);

        $documentTypes = $documentTypeRepository->findAllAsArray();

        $disciplines = $disciplineRepository->findAllAsGroupedArray();

        return $this->render('documents/index.html.twig', [
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'disciplineGroups' => $disciplines,
            'locations' => $locations
        ]);
    }

    /**
     * @Route("/document/", name="document", methods={"GET"})
     * @param Request $request
     * @param DocumentRepository $documentRepository
     * @param DocumentDraftRepository $documentDraftRepository
     * @return Response
     * @throws \Exception
     * @IsGranted("ROLE_USER")
     */
    public function show(Request $request, DocumentRepository $documentRepository, DocumentDraftRepository $documentDraftRepository)
    {
        if (!$id = $request->query->get("documentId"))
        {
            throw new \Exception("Invalid document");
        }
        $document = $documentRepository->find($id);

        $history = $document->getDocumentHistories();

        $canDoRevision = true;

        $maxDeadlineDate = new \DateTime($this->getParameter("max_revision_time"));
        $minDeadlineDate = new \DateTime("now +1 day");
        $defaultDeadlineDate = new \DateTime("now +2 weeks");

        if (
            $documentDraftRepository->getOpenDocumentDraftsByDocument($document) || $document->getIssue())
        {
            $canDoRevision = false;
        }

        if (!$document)
        {
            throw new \Exception("Unknown document number");
        }

        return $this->render('documents/show.html.twig', [
            'document' => $document,
            'documentHistory' => $history,
            'canDoRevision' => $canDoRevision,
            'maxDeadlineDate' => $maxDeadlineDate,
            'minDeadlineDate' => $minDeadlineDate,
            'defaultDeadlineDate' => $defaultDeadlineDate
        ]);
    }

    /**
     * @Route("/ajax/documents/", name="ajax.documents", methods={"GET"})
     * @param Request $request
     * @param DocumentRepository $documentRepository
     * @return JsonResponse|Response
     * @IsGranted("ROLE_USER")
     */
    public function ajax_filter_documents(Request $request, DocumentRepository $documentRepository)
    {
        // Deny non-ajax requests
        if (!$request->isXmlHttpRequest()) {
            return $this->render("pages/blank.html.twig", ['message' => "This page doesn't exist"]);
        }

        $buildings = $request->query->get('buildings');
        $disciplineGroups = $request->query->get("disciplineGroup");
        $documentTypes = $request->query->get("documentTypes");
        $floor = $request->query->get('floor');

        /** @var ArrayCollection|Document[] $documents */
        $documents = $documentRepository->findWithFilter($this->getUser(), $buildings, $disciplineGroups, $floor, $documentTypes);

        $jsonData = array();

        foreach ($documents as $document){
            array_push($jsonData, [
                'naam' => $document->getDocumentName(),
                'version' => $document->getVersion(),
                'discipline' => $document->getDiscipline()->getCode(),
                'omschrijving' => $document->getDiscipline()->getDescription(),
                'gebouw' => $document->getBuilding()->getName(),
                'verdieping' => $document->getFloor(),
                'documentId' => $document->getId(),
                'documentType' => $document->getDocumentType()->getName(),
                'area' => $document->getArea()
            ]);
        }
        return new JsonResponse($jsonData);
    }

}
