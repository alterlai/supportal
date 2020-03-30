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
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;


class DocumentFixtures extends Fixture implements DependentFixtureInterface
{
    private $disciplines;

    public function __construct(DisciplineRepository $dr)
    {
        $this->disciplines = $dr->findAll();
    }

    /**
     * @inheritDoc
     */
    public function load(\Doctrine\Persistence\ObjectManager $manager)
    {
        $loader = new NativeLoader();
        $objectset = $loader->loadData([\App\Entity\Document::class => [
            'document{1..10}' => [
                'discipline' => $this->getRandomDiscipline(),
                'file_name' => "testfile.pdf",
                'updated_at' => '<dateTime("now")>',
                'description' => '<text()>'
            ]
        ]])->getObjects();

        foreach ($objectset as $object)
        {
            print('object');
            $manager->persist($object);
        }
        $manager->flush();
    }

    public function getRandomDiscipline() : Discipline {
        $key = array_rand($this->disciplines);
        return $this->disciplines[$key];
    }


    public function getDependencies()
    {
        return array(
            DisciplineFixtures::class,
            BuildingFixtures::class
        );
    }
}
