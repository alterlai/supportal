<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/settings", name="settings")
     */
    public function index()
    {
        return $this->render('profile/settings.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
