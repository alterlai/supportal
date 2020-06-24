<?php

namespace App\Command;

use App\Entity\Discipline;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDisciplinesCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->entityManager = $em;
    }

    protected function configure()
    {
        $this
            ->setName('iqsupport:import:disciplines')
            ->setDescription('Import disciplines CSV into Doctrine');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Attempting to import disciplines...");

        $reader = Reader::createFromPath('%kernel.root_dir%/../csv/nlsfb.csv', 'r');

        $reader->setDelimiter(";");

        $headers = $reader->fetchOne();     // Get headers

        $reader->setHeaderOffset(0);    // skip header row

        $data = $reader->getRecords($headers);

        foreach($data as  $row)
        {
            $discipline = (new Discipline())
                ->setDescription($row['description'])
                ->setCode((float) $row['code']);
            $this->entityManager->persist($discipline);
        }

        $this->entityManager->flush();

        $io->success("Disciplines imported!");

        return 0;
    }
}