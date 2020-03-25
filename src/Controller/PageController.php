<?php

namespace App\Controller;

use App\Service\DocumentNamer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="pages")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render("pages/home.html.twig");
    }

    /**
     * @Route("/buildings", name="buildings")
     * @IsGranted("ROLE_USER")
     */
    public function buildings() {
        return $this->render("pages/buildings.html.twig");
    }
}
