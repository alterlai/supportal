<?php

namespace App\Command;

use App\Service\ReportingService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendUserActivityReportCommand extends Command
{
    protected static $defaultName = 'SendUserActivityReportCommand';
    private $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        parent::__construct();
        $this->reportingService = $reportingService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Compile user activity and send it to the recipient.')
            ->addArgument('recipient', InputArgument::REQUIRED, 'Email of the recipient e.g. test@gmail.com')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $recipient = $input->getArgument('recipient');

        if (!$recipient) {
            $io->error("Please specify a recipient email as the first argument");
        }

        $io->note(sprintf('Generating user activity reports...'));

        $

        $io->note(sprintf('Sending user activity report to: %s', $recipient));

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
