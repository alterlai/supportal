<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Discipline;
use App\Entity\Document;
use App\Entity\DocumentType;
use App\Repository\BuildingRepository;
use App\Repository\DisciplineRepository;
use App\Repository\UserRepository;
use App\Service\DocumentNameParserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use League\Csv\Reader;
use Nelmio\Alice\Loader\NativeLoader;


class DocumentFixtures extends Fixture implements DependentFixtureInterface
{
    private $buildings;
    private $nameParser;
    private $documentTypes = array();
    private $disciplines = array();
    private $userRepository;

    public function __construct(DisciplineRepository $dr, BuildingRepository $br, UserRepository $userRepository)
    {
        $this->buildings = $br->findAll();
        $this->nameParser = new DocumentNameParserService();
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->loadDocumentTypes($manager);
        $this->loadDisciplines($manager);
        $adminUser = $this->userRepository->findOneBy(["username" => "admin"]);
        $loader = new NativeLoader();
        /** @var Document[] $objectset */
        $objectset = $loader->loadData(
            [\App\Entity\Document::class =>
                [
                    'document{1..400}' => [
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
            $object->setBuilding($randomBuilding);
            $object->setLocation($this->getReference(LocationFixtures::GRONINGEN));
            $object->setFileName($this->generateRandomFilename($randomDiscipline, $randomDocumentType, $randomBuilding, $randomFloor));
            $object->setPdfFilename("");
            $object->setUploadedBy($adminUser);
            $object->setVersion(1);

            $manager->persist($object);
        }
        $manager->flush();
    }


    public function generateRandomFilename(Discipline $discipline, DocumentType $documentType, Building $randomBuilding, int $floor): string
    {
        return $this->nameParser->generateFileNameFromEntities($randomBuilding, $discipline, $documentType , 1, ".dwg", $floor);
    }

    private function loadDocumentTypes(ObjectManager $manager)
    {
        $reader = Reader::createFromPath('%kernel.root_dir%/../csv/documentTypes.csv', 'r');
        $reader->addStreamFilter('convert.iconv.Windows-1252/UTF-8');

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

    private function loadDisciplines(ObjectManager $manager) {
        $reader = Reader::createFromPath('%kernel.root_dir%/../csv/nlsfb.csv', 'r');
        $reader->addStreamFilter('convert.iconv.Windows-1252/UTF-8');

        $reader->setDelimiter(";");

        $headers = $reader->fetchOne();     // Get headers

        $reader->setHeaderOffset(0);    // skip header row

        $data = $reader->getRecords($headers);

        foreach($data as  $row)
        {
            $discipline = (new Discipline())
                ->setDescription($row['description'])
                ->setCode((float) $row['code']);
            array_push($this->disciplines, $discipline);
            $manager->persist($discipline);
        }

        $manager->flush();
    }

    private function getRandomDocumentType()
    {
        $key = array_rand($this->documentTypes);
        return $this->documentTypes[$key];
    }


    public function getRandomDiscipline() : Discipline
    {
        $key = array_rand($this->disciplines);
        return $this->disciplines[$key];
    }

    public function getRandomBuilding()
    {
        $i = random_int(1, 5);

        /** @var Building $building */
        $building = $this->getReference("building$i");
        return $building;
    }


    public function getDependencies()
    {
        return array(
            BuildingFixtures::class
        );
    }
}
