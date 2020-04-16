<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Discipline;
use App\Entity\Document;
use App\Entity\DocumentType;
use App\Repository\BuildingRepository;
use App\Repository\DisciplineRepository;
use App\Service\DocumentNameParserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use League\Csv\Reader;
use Nelmio\Alice\Loader\NativeLoader;


class DocumentFixtures extends Fixture implements DependentFixtureInterface
{
    private $disciplines;
    private $buildings;
    private $nameParser;
    private $documentTypes = array();

    public function __construct(DisciplineRepository $dr, BuildingRepository $br)
    {
        $this->disciplines = $dr->findAll();
        $this->buildings = $br->findAll();
        $this->nameParser = new DocumentNameParserService();
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->loadDocumentTypes($manager);
        $loader = new NativeLoader();
        /** @var Document[] $objectset */
        $objectset = $loader->loadData(
            [\App\Entity\Document::class =>
                [
                    'document{1..40}' => [
                        'updated_at' => '<dateTime("now")>',
                        'description' => '<text()>',
                    ]
                ]
            ]
        )->getObjects();

        foreach ($objectset as $object)
        {
            // Generate a few random properties
            $randomDiscipline = $this->getRandomDiscipline();
            $randomBuilding = $this->getRandomBuilding();
            $randomDocumentType = $this->getRandomDocumentType();
            $randomFloor = rand(0, 4);

            // Set random properties
            $object->setDiscipline($randomDiscipline);
            $object->setFloor($randomFloor);
            $object->setDocumentType($randomDocumentType);
            $object->setLocation($this->getReference(LocationFixtures::GRONINGEN));
            $object->setFileName($this->generateRandomFilename($randomDiscipline, $randomDocumentType, $randomBuilding, $randomFloor));

            $manager->persist($object);
        }
        $manager->flush();
    }

    public function getRandomDiscipline() : Discipline
    {
        $key = array_rand($this->disciplines);
        return $this->disciplines[$key];
    }

    public function getRandomBuilding(): Building
    {
        $key = array_rand($this->buildings);

        return $this->buildings[$key];
    }

    public function generateRandomFilename(Discipline $discipline, DocumentType $documentType, Building $randomBuilding, int $floor): string
    {
        return $this->nameParser->generateFileNameFromEntities($randomBuilding, $discipline, $documentType, $floor , 1, ".pdf");
    }

    private function loadDocumentTypes(ObjectManager $manager)
    {
        $reader = Reader::createFromPath('%kernel.root_dir%/../csv/documentTypes.csv', 'r');

        $reader->setDelimiter(";");

        $headers = $reader->fetchOne();     // Get headers

        $reader->setHeaderOffset(0);    // skip header row

        $data = $reader->getRecords($headers);

        foreach($data as  $row)
        {
            $documentType = ( new DocumentType())
                ->setCode($row['Code'])
                ->setName($row['Benaming']);
            array_push($this->documentTypes, $documentType);
            $manager->persist($documentType);
        }

        $manager->flush();
    }

    private function getRandomDocumentType()
    {
        $key = array_rand($this->documentTypes);
        return $this->documentTypes[$key];
    }


    public function getDependencies()
    {
        return array(
            DisciplineFixtures::class,
            BuildingFixtures::class
        );
    }
}
