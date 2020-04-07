<?php

namespace App\DataFixtures;

use App\Entity\Building;
use App\Entity\Discipline;
use App\Entity\Document;
use App\Repository\BuildingRepository;
use App\Repository\DisciplineRepository;
use App\Service\DocumentNameParserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Nelmio\Alice\Loader\NativeLoader;


class DocumentFixtures extends Fixture implements DependentFixtureInterface
{
    private $disciplines;
    private $buildings;
    private $nameParser;

    public function __construct(DisciplineRepository $dr, BuildingRepository $br)
    {
        $this->disciplines = $dr->findAll();
        $this->buildings = $br->findAll();
        $this->nameParser = new DocumentNameParserService();
    }

    /**
     * @inheritDoc
     */
    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        $loader = new NativeLoader();
        /** @var Document[] $objectset */
        $objectset = $loader->loadData(
            [\App\Entity\Document::class =>
                [
                    'document{1..40}' => [
                        'updated_at' => '<dateTime("now")>',
                        'description' => '<text()>'
                    ]
                ]
            ]
        )->getObjects();

        foreach ($objectset as $object)
        {
            $randomDiscipline = $this->getRandomDiscipline();

            $randomBuilding = $this->getRandomBuilding();



            $object->setDiscipline($randomDiscipline);

            $object->setFileName($this->generateRandomFilename($randomDiscipline, $randomBuilding));

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

    public function generateRandomFilename(Discipline $discipline, Building $randomBuilding): string
    {
        return $this->nameParser->generateFileNameFromEntities($randomBuilding, $discipline, rand(0, 4), 1, ".pdf");
    }


    public function getDependencies()
    {
        return array(
            DisciplineFixtures::class,
            BuildingFixtures::class
        );
    }
}
