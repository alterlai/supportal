<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\InvalidReportGroupingException;
use App\Repository\UserActionRepository;
use App\Repository\UserRepository;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ReportingService
{
    private $userActionRepository;
    private $paramterbag;
    /** @var Environment */
    private $templating;
    private $report_dir;
    private $userRepository;

    public function __construct(UserActionRepository $userActionRepository,
                                ParameterBagInterface $parameterBag,
                                Environment $twig,
                                UserRepository $userRepository
    )
    {
        $this->userActionRepository = $userActionRepository;
        $this->templating = $twig;
        $this->paramterbag = $parameterBag;
        $this->report_dir = $this->paramterbag->get("report_directory");
        $this->userRepository = $userRepository;
    }


    /**
     * Provide this function with a from and to date and it will return a location to a zipped file containing PDF files.
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $grouping
     * @return Response
     * @throws InvalidReportGroupingException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Exception
     */
    public function generateReportFile(\DateTime $from, \DateTime $to, string $grouping)
    {
        $tempdir = $this->generateTempDirectory();

        if ($grouping == "user")
        {
            $allUsers = $this->userRepository->findAll();
            foreach ($allUsers as $user)
            {
                $userData = $this->userActionRepository->getFromUser($user, $from, $to);
                // If there is actual userdata
                if($userData)
                {
                    $this->generatePDFFile($tempdir, $userData, $from, $to);
                }
            }
        }
        elseif ($grouping == "org")
        {
            $data = $this->userActionRepository->getGroupedByOrganisation($from, $to);
        }
        else
        {
            throw new InvalidReportGroupingException("Onbekende grouping value.");
        }

        return $tempdir;
    }

    /**
     * Generate a PDF file in a given location from provided data
     * @param string $location file location
     * @param $data
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generatePDFFile(string $location, array $userData, DateTime $from, DateTime $to)
    {
        $pdfOptions = new Options();
        $pdfOptions->set("defaultFont", "Arial");
        $abs_img = $this->paramterbag->get("absolute_image_directory");
        $abs_proj = $this->paramterbag->get("kernel.project_dir");


        $pdf = new Dompdf($pdfOptions);

        $html = $this->templating->render("pdf/user_report.html.twig", [
            'data' => $userData,
            'imgdir' => $abs_img,
            'projectdir' => $abs_proj,
            'from' => $from,
            'to' => $to
        ]);

        $pdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $pdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $pdf->render();

        // Store PDF Binary Data
        $output = $pdf->output();

        $username = $userData[0]["username"];

        $pdfFilepath =  $location . '/'.$username.'.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // Send some text response
        return $pdfFilepath;

    }

    /**
     * Generate a random temporary directory to put the files in.
     * @return string absolute directory path
     * @throws \Exception
     */
    private function generateTempDirectory()
    {
        $dirname = uniqid();

        $fs = new Filesystem();

        $fs->mkdir($this->report_dir.$dirname);

        return $this->report_dir.$dirname;
    }
}