<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\InvalidReportGroupingException;
use App\Repository\BuildingRepository;
use App\Repository\OrganisationRepository;
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
    private $generatedFiles;
    private $organisationRepository;
    private $buildingRepository;

    public function __construct(UserActionRepository $userActionRepository, ParameterBagInterface $parameterBag, Environment $twig, UserRepository $userRepository, OrganisationRepository $organisationRepository, BuildingRepository $buildingRepository)
    {
        $this->userActionRepository = $userActionRepository;
        $this->templating = $twig;
        $this->paramterbag = $parameterBag;
        $this->report_dir = $this->paramterbag->get("report_directory");
        $this->userRepository = $userRepository;
        $this->generatedFiles = [];
        $this->organisationRepository = $organisationRepository;
        $this->buildingRepository = $buildingRepository;
    }


    /**
     * Provide this function with a from and to date and it will return a location to a zipped file containing PDF files.
     * @param \DateTime $from
     * @param \DateTime $to
     * @param string $grouping
     * @return string absolute filepath to generated zip file.
     * @throws InvalidReportGroupingException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Exception
     */
    public function GenerateReport(\DateTime $from, \DateTime $to, string $grouping)
    {
        // Temporary directory for PDF files
        $tempdir = $this->generateTempDirectory();

        switch ($grouping)
        {
            case "user":
                $allUsers = $this->userRepository->findAll();
                foreach ($allUsers as $user)
                {
                    $userData = $this->userActionRepository->getFromUser($user, $from, $to);
                    // If there is actual userdata
                    if($userData)
                    {
                        $this->generateWithUserTemplate($userData, $from, $to, $tempdir);
                    }
                }
                break;
            case "org":
                $allOrganisations = $this->organisationRepository->findAll();
                foreach ($allOrganisations as $organisation)
                {
                    $organisationData = $this->userActionRepository->getGroupedByOrganisation($organisation, $from, $to);
                    // If there is actual userdata
                    if($organisationData)
                    {
                        $this->generateWithOrganisationTemplate($organisationData, $from, $to, $tempdir);
                    }
                }
                break;
            case "building":
                $allBuildings = $this->buildingRepository->findAll();
                foreach ($allBuildings as $building)
                {
                    $buildingData = $this->userActionRepository->getGroupedByBuilding($building, $from, $to);
                    // If there is actual data
                    if ($buildingData){
                        $this->generateWithBuildingTemplate($buildingData, $from, $to, $tempdir);
                    }
                }
                break;


            default:
                throw new InvalidReportGroupingException("Onbekende grouping value.");
        }


        return $this->createArchive($tempdir);
    }

    /**
     * Generate User HTML template and call PDf generation function
     * @param array $data
     * @param DateTime $from
     * @param DateTime $to
     * @param string $absolutePDFDirectory file location
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function generateWithUserTemplate(array $data, DateTime $from, DateTime $to, string $absolutePDFDirectory)
    {
        $abs_img = $this->paramterbag->get("absolute_image_directory");

        $abs_proj = $this->paramterbag->get("kernel.project_dir");

        $filename = ($data[0]['username']).".pdf";

        $html = $this->templating->render("pdf/user_report.html.twig", [
            'data' => $data,
            'imgdir' => $abs_img,
            'projectdir' => $abs_proj,
            'from' => $from,
            'to' => $to
        ]);

        return $this->generatePDFFile($html, $absolutePDFDirectory, $filename);
    }

    /**
     * Generate Organisation HTML template and call PDf generation function
     * @param array $data
     * @param DateTime $from
     * @param DateTime $to
     * @param string $absolutePDFDirectory
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function generateWithOrganisationTemplate(array $data, DateTime $from, DateTime $to, string $absolutePDFDirectory)
    {
        $abs_img = $this->paramterbag->get("absolute_image_directory");

        $abs_proj = $this->paramterbag->get("kernel.project_dir");

        $filename = $data[0]['organisation'].".pdf";

        $html =  $this->templating->render("pdf/organisation_report.html.twig", [
            'data' => $data,
            'imgdir' => $abs_img,
            'projectdir' => $abs_proj,
            'from' => $from,
            'to' => $to
        ]);

        return $this->generatePDFFile($html, $absolutePDFDirectory, $filename);
    }

    private function generateWithBuildingTemplate(array $data, DateTime $from, DateTime $to, string $absolutePDFDirectory)
    {
        $abs_img = $this->paramterbag->get("absolute_image_directory");

        $abs_proj = $this->paramterbag->get("kernel.project_dir");

        $filename = $data[0]['building'].".pdf";

        $html =  $this->templating->render("pdf/building_report.html.twig", [
            'data' => $data,
            'imgdir' => $abs_img,
            'projectdir' => $abs_proj,
            'from' => $from,
            'to' => $to
        ]);

        return $this->generatePDFFile($html, $absolutePDFDirectory, $filename);
    }


    /**
     * Generate PDF files on disk with given template.
     * @param $html
     * @param $absolutePDFDirectory
     * @param $filename
     * @return string
     */
    private function generatePDFFile($html, $absolutePDFDirectory, $filename)
    {
        $pdfOptions = new Options();
        $pdfOptions->set("defaultFont", "Arial");


        $pdf = new Dompdf($pdfOptions);

        $pdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $pdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $pdf->render();

        // Store PDF Binary Data
        $output = $pdf->output();

        $pdfFilepath =  $absolutePDFDirectory.'/'.$filename;

        // Save the filename to generate a ZIP later
        array_push($this->generatedFiles, $filename);

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

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

    /**
     * Create a zip archive for download.
     * @param string pdf file directory
     * @return string filename of created zip file
     */
    private function createArchive(string $absolutePDFDirectory)
    {
        $filename = $absolutePDFDirectory."/archive.zip";
        $zip = new \ZipArchive();

        // https://www.php.net/manual/en/zip.examples.php
        if ($zip->open($filename, \ZipArchive::CREATE)!==TRUE) {
            exit("cannot open <$filename>\n");
        }

        foreach ($this->generatedFiles as $localPDFFileName)
        {
            $zip->addFile($absolutePDFDirectory."/$localPDFFileName", $localPDFFileName);
        }
        $zip->close();

        return $filename;
    }
}
