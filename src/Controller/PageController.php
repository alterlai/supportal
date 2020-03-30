<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render("pages/home.html.twig");
    }

    /**
     * @Route("/buildings/", name="buildings")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function showBuildings()
    {

        /* @var User $user */
        $user = $this->getUser();

        $buildings = $user->getOrganisation()->getBuildings();

        return $this->render("pages/buildings.html.twig", ['buildings' => $buildings]);
    }

     /**
     * @Route("/documents/{buildingId}", name="documents")
     * @param int $buildingId
     * @return Response
     */
    public function showDocuments(int $buildingId, BuildingRepository $buildingRepository, DocumentRepository $documentRepository)
    {
        $building = $buildingRepository->find($buildingId);
        $documents = $documentRepository->findAll();
        return $this->render("pages/documents.html.twig", ['building' => $building, 'documents' => $documents]);
    }

    /**
     * @Route("/disciplines/{buildingId}", name="disciplines")
     * @param int $buildingId
     * @param BuildingRepository $buildingRepository
     * @return Response
     */
    public function showDisciplines(int $buildingId, BuildingRepository $buildingRepository)
    {
        $building = $buildingRepository->find($buildingId);
        return $this->render("pages/disciplines.html.twig", ['building' => $building]);
    }
}
