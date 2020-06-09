<?php

namespace App\Controller;

use App\Repository\UserActionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReportingController extends AbstractController
{
    /**
     * @Route("/admin/reporting", name="admin.reporting")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserActionRepository $userActionRepository)
    {
        $data = $userActionRepository->getGroupedByUser();

        return $this->render('reporting/index.html.twig', ['data' => $data]);
    }
}
