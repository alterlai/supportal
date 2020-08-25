<?php

namespace App\Command;


use App\Entity\DraftStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDraftStatusesCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('iqsupport:import:draftstatus')
            ->setDescription('Import draft statuses into the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $draftStatuses = ["Geaccepteerd", "In behandeling", "Afgekeurd"];
        $statusDescription = [
            "Succesvol afgerond",
            "De tekening is in behandeling. U wordt op de hoogte gesteld als er meer informatie is.",
            "De tekening is afgekeurd. Bekijk de aanvraag voor meer informatie."
        ];

        try {
            foreach ($draftStatuses as $i => $draftStatus)
            {
                $status = (new DraftStatus())
                    ->setName($draftStatus)
                    ->setDescription($statusDescription[$i]);
                $this->em->persist($status);
            }
            $this->em->flush();
        }
        catch (\Exception $e)
        {
            $io->error("Something went wrong import draft statuses");
            $io->error($e);
        }

        return 0;
    }
}