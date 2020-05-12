<?php

namespace App\Controller;

use App\Repository\BuildingRepository;
use App\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MockDocumentsController extends AbstractController
{
    /**
     * @Route("/documents/{buildingId}/{disciplineCode}", name="documents")
     * @IsGranted("ROLE_USER")
     * @param int $buildingId
     * @param string $disciplineCode
     * @param BuildingRepository $buildingRepository
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function showDocuments(int $buildingId, string $disciplineCode, DocumentRepository $documentRepository)
    {
        $documents = array();
        if ($disciplineCode == 'B') $documents = $documentRepository->findByDisciplineRange(11, 50, $buildingId);
        if ($disciplineCode == 'W') $documents = $documentRepository->findByDisciplineRange(51, 59, $buildingId);
        if ($disciplineCode == 'E') $documents = $documentRepository->findByDisciplineRange(60, 70, $buildingId);
        if ($disciplineCode == 'F') $documents = $documentRepository->findByDisciplineRange(70, 80, $buildingId);
        return $this->render("pages/documents.html.twig", ['documents' => $documents]);
    }
}
