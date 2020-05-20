<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param DocumentRepository $documentRepository
     * @return Response
     */
    public function index()
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $locations = $currentUser->getOrganisation()->getLocations();
        $issues = $currentUser->getIssues();

        return $this->render("pages/home.html.twig", ['locations' => $locations, 'issues' => $issues]);
    }

}
