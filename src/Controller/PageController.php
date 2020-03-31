<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BuildingRepository;
use App\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/disciplines/{buildingId}", name="disciplines")
     * @IsGranted("ROLE_USER")
     * @param int $buildingId
     * @param BuildingRepository $buildingRepository
     * @return Response
     */
    public function showDisciplines(int $buildingId, BuildingRepository $buildingRepository)
    {
        $building = $buildingRepository->find($buildingId);
        return $this->render("pages/disciplines.html.twig", ['building' => $building]);
    }

    /**
     * @Route("/search/", name="search")
     * @return Response
     * @throws \Exception
     */
    public function searchDocuments(DocumentRepository $documentRepository, Request $request) {
        if (!$request->get("search")) throw new \Exception("Vul een geldige zoekterm in");
        $documents = $documentRepository->searchAllColumns($request->get("search"));
        return $this->render("pages/documents.html.twig", ['documents' => $documents]);
    }
}
