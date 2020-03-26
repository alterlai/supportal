<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Discipline;
use App\Entity\Document;
use App\Repository\BuildingRepository;
use App\Repository\DisciplineRepository;
use App\Service\DocumentNameParserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class DocumentFixtures extends Fixture
{
    private $buildings;
    private $disciplines;
    private $fileNameParser;

    private function __construct(BuildingRepository $buildingRepo, DocumentNameParserService $fileNameParser, DisciplineRepository $disciplineRepository)
    {
        $this->buildings = $buildingRepo->findAll();
        $this->disciplines = $disciplineRepository->findAll();
        $this->fileNameParser = $fileNameParser;
    }

    public function load(ObjectManager $manager)
    {
        $randomBuilding = $this->getRandomBuilding();
        $floor = rand(0, 3);
        $randomDiscipline = $this->getRandomDiscipline();
        $description = "Randomly generated document";
        $updated_at = new \DateTime("now");
        $filename = $this->fileNameParser->generateFileNameFromEntities($randomBuilding, $randomDiscipline, $floor, 1);

        $document = (new Document())
            ->setFileName($filename)
            ->setDiscipline($randomDiscipline)
            ->setUpdatedAt($updated_at)
            ->setDescription($description);
        $manager->persist($document);

        $manager->flush();
    }

    private function getRandomBuilding() : Building
    {
        /** @var Building $building */
        $randomBuilding = shuffle($this->buildings)[0];

        return $randomBuilding;
    }

    private function getRandomDiscipline() : Discipline
    {
        $randomDiscipline = shuffle($this->disciplines)[0];

        return $randomDiscipline;
    }
}
