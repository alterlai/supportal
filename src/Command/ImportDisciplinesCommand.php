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
            ->setName('import:disciplines')
            ->setDescription('Import disciplines CSV into Doctrine');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Attempting to import disciplines...");

        $reader = Reader::createFromPath('%kernel.root_dir%/../csv/nlsfb.csv', 'r');

        $headers = $reader->fetchOne();

        $data = $reader->getRecords($headers);
        while ($row = $data->current())
        {
            $discipline = (new Discipline())
                ->setDescription($row[1])
                ->setCode($row[0]);
            $this->entityManager->persist($discipline);
            $data->next();
        }

        $this->entityManager->flush();

        $io->success("Disciplines imported!");
    }
}