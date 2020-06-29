<?php

namespace App\Command;

use App\Entity\DocumentType;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportDocumentTypesCommand extends Command
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
            ->setName('iqsupport:import:documentTypes')
            ->setDescription('Import document types CSV into Doctrine');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Attempting to import document types...");

        $reader = Reader::createFromPath('%kernel.root_dir%/../csv/documentTypes.csv', 'r');

        $reader->setDelimiter(";");

        $headers = $reader->fetchOne();     // Get headers

        $reader->setHeaderOffset(0);    // skip header row

        $data = $reader->getRecords($headers);

        foreach($data as  $row)
        {
            $documentType = (new DocumentType())
                ->setName($row['Benaming'])
                ->setCode((float) $row['Code']);
            $this->entityManager->persist($documentType);
        }

        $this->entityManager->flush();

        $io->success("Document Types imported!");

        return 0;
    }
}