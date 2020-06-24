<?php

namespace App\Command;

use App\Exception\InvalidReportGroupingException;
use App\Service\MailerService;
use App\Service\ReportingService;
use phpDocumentor\Reflection\TypeResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendUserActivityReportCommand extends Command
{
    private $reportingService;
    private $mailerService;

    public function __construct(ReportingService $reportingService, MailerService $mailerService)
    {
        parent::__construct();
        $this->reportingService = $reportingService;
        $this->mailerService = $mailerService;
    }

    protected function configure()
    {
        $this
            ->setName('iqsupport:reporting:mail')
            ->setDescription('Compile user activity and send it to the recipient.')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Email of the recipient e.g. test@gmail.com')
            ->addArgument('grouping', InputArgument::REQUIRED, 'Grouping by user or organisation. use \'org\' or \'user\'')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $recipient = $input->getArgument('recipient');
        $grouping = $input->getArgument('grouping');

        try {
            $io->note(sprintf('Generating user activity reports...'));

            $reportFilePath = $this->reportingService->GenerateReport(new \DateTime("now -1 month"), new \DateTime("now"), $grouping);

            $io->note(sprintf('Sending user activity report to: %s', $recipient));

            $this->mailerService->sendUserReportMail($recipient, $reportFilePath);
        }
        catch(InvalidReportGroupingException $e) {
            $io->error("Invalid grouping. Use org or user");
            die();
        }
        catch (\Exception $e) {
            $io->error("Something went wrong during creating of the user reports... $e");
            die();
        }

        $io->success('User reports sent!');

        return 0;
    }
}
