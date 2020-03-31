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
     * @Route("/documents/{buildingCode}/{disciplineCode}", name="documents")
     * @IsGranted("ROLE_USER")
     * @param string $buildingCode
     * @param string $disciplineCode
     * @return Response
     */
    public function showDocuments(string $buildingCode, string $disciplineCode, BuildingRepository $buildingRepository, DocumentRepository $documentRepository)
    {
        $documents = array();
        if ($disciplineCode == 'B') $documents = $documentRepository->findByDisciplineRange(11, 50);
        if ($disciplineCode == 'W') $documents = $documentRepository->findByDisciplineRange(51, 59);
        if ($disciplineCode == 'E') $documents = $documentRepository->findByDisciplineRange(60, 70);
        if ($disciplineCode == 'F') $documents = $documentRepository->findByDisciplineRange(70, 80);
        return $this->render("pages/documents.html.twig", ['documents' => $documents]);
    }
}
