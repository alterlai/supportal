<?php

namespace App\Controller;

use App\Repository\DisciplineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController {

    /**
     * @Route("/test/")
     */
    public function test(DisciplineRepository $repo)
    {
        $groups = $repo->findGroups();

        var_dump($groups);
        die();
    }
}