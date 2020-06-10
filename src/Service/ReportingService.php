<?php

namespace App\Service;

use App\Exception\InvalidReportGroupingException;
use App\Repository\UserActionRepository;
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

    public function __construct(UserActionRepository $userActionRepository,
                                ContainerInterface $containerInterface,
                                ParameterBagInterface $parameterBag,
                                Environment $twig
    )
    {
        $this->userActionRepository = $userActionRepository;
//        $this->templating = $containerInterface->get('templating');
        $this->templating = $twig;
        $this->paramterbag = $parameterBag;
        $this->report_dir = $this->paramterbag->get("report_directory");
    }


    /**
     * Provide this function with a from and to date and it will return a location to a zipped file containing PDF files.
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $grouping
     * @return string
     * @throws InvalidReportGroupingException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generateReportFile(\DateTime $from, \DateTime $to, string $grouping)
    {
        if ($grouping == "user")
        {
            $data = $this->userActionRepository->getGroupedByUser($from, $to);
        }
        elseif ($grouping == "org")
        {
            $data = $this->userActionRepository->getGroupedByOrganisation($from, $to);
        }
        else
        {
            throw new InvalidReportGroupingException("Onbekende grouping value.");
        }

        $tempdir = $this->generateTempDirectory();

        return $this->generatePDFFile($tempdir, $data);
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
    public function generatePDFFile(string $location, $data)
    {
        $pdfOptions = new Options();
        $pdfOptions->set("defaultFont", "Arial");

        $pdf = new Dompdf($pdfOptions);

        $html = $this->templating->render("pdf/user_report.html.twig", ['title' => "test pdf"]);

        $pdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $pdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $pdf->render();

        // Store PDF Binary Data
        $output = $pdf->output();

        $pdfFilepath =  $location . '/mypdf.pdf';
        // TODO: change filename to username.

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // Send some text response
        return $pdfFilepath;

    }

    /**
     * Generate a random temporary directory to put the files in.
     * @return string
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
