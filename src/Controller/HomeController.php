<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render("home/test.html.twig", ['test' => "Test variabele"]);
    }

    /**
     * @Route("/welcome", name="welcome")
     */
    public function welcome() {
        return $this->render("home/welcome.html.twig");
    }
}
