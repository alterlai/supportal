<?php

namespace App\Controller;

use App\Entity\UserAction;
use App\Form\ReportType;
use App\Repository\UserActionRepository;
use App\Repository\UserRepository;
use App\Service\ReportingService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        else
        {
            return $this->render('reporting/index.html.twig', [
                'form' => $form->createView()
            ]);
        }

        return new Response ($file);
    }

    /**
     * @Route("/admin/temp/pdf", name="preview.pdf")
     * @IsGranted("ROLE_ADMIN")
     */
    public function viewPDF(UserRepository $userRepository, UserActionRepository $userActionRepository)
    {
        $from = new \DateTime("now -2 weeks");
        $to = new \DateTime("now");
        $admin = $userRepository->find(6);
        $data = $userActionRepository->getFromUser($admin, $from, $to);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf/user_report.html.twig', [
            'data' => $data,
            'imgdir' => $this->getParameter("absolute_image_directory"),
            'projectdir' => $this->getParameter("kernel.project_dir"),
            'from' => $from,
            'to' => $to
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
        die();
    }

}
