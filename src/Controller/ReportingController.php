<?php

namespace App\Controller;

use App\Form\ReportType;
use App\Service\MailerService;
use App\Service\ReportingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReportingController extends AbstractController
{
    /**
     * @Route("/admin/reporting", name="admin.reporting")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param ReportingService $reportingService
     * @return string
     * @throws \App\Exception\InvalidReportGroupingException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index(Request $request, ReportingService $reportingService)
    {
        $form = $this->createForm(ReportType::class, ['action' => $this->generateUrl('admin.reporting')]);
        $form->handleRequest($request);

        // Handle submitted draft form when the user clicks accept.
        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getdata();
            $file = $reportingService->generateReportFile($data['from'], $data['to'], $data['grouping']);
        }
        else {
            return $this->render('reporting/index.html.twig', [
                'form' => $form->createView()
            ]);
        }

        return $this->file($file);
    }
}
