<?php

namespace App\Controller;

use App\Repository\BuildingRepository;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LocationsController extends AbstractController
{
    /**
     * @Route("/locations/{locationId}", name="locations", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param integer $locationId
     * @param BuildingRepository $buildingRepository
     * @return Response
     */
    public function index(int $locationId, BuildingRepository $buildingRepository)
    {
        $buildings = $buildingRepository->findByUserAndLocation($this->getUser(), $locationId);
        return $this->render('pages/buildings.html.twig', ['buildings' => $buildings]);
    }
}