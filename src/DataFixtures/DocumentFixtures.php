<?php

namespace App\DataFixtures;

use App\Entity\Discipline;
use App\Entity\Document;
use App\Faker\NewNativeLoader;
use App\Faker\Provider\DocumentProvider;
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
                    'document{1..10}' => [
                        'updated_at' => '<dateTime("now")>',
                        'description' => '<text()>'
                    ]
                ]
            ]
        )->getObjects();

        foreach ($objectset as $object)
        {
            $randomDiscipline = $this->getRandomDiscipline();

            $object->setFileName($this->generateRandomFilename($randomDiscipline));

            $object->setDiscipline($this->getRandomDiscipline());

            $manager->persist($object);
        }
        $manager->flush();
    }

    public function getRandomDiscipline() : Discipline
    {
        $key = array_rand($this->disciplines);
        return $this->disciplines[$key];
    }

    public function generateRandomFilename(Discipline $discipline): string
    {
        $key = array_rand($this->buildings);

        $randomBuilding = $this->buildings[$key];

        return $this->nameParser->generateFileNameFromEntities($randomBuilding, $discipline, rand(0, 4), 1);
    }


    public function getDependencies()
    {
        return array(
            DisciplineFixtures::class,
            BuildingFixtures::class
        );
    }
}
