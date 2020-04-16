<?php

namespace App\DataFixtures;

use App\Entity\Building;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BuildingFixtures extends Fixture implements DependentFixtureInterface
{

    public const BUILDINGS_REFERENCE = "buildings";

    public function load(ObjectManager $manager)
    {
        $building = (new Building())
            ->setName("Van Doorenveste")
            ->setCode("ZP11")
            ->setLocation($this->getReference(LocationFixtures::GRONINGEN));
        $this->setReference('building1', $building);
        $manager->persist($building);

        $building = (new Building())
            ->setName("Van Olsttoren")
            ->setCode("ZP09")
            ->setLocation($this->getReference(LocationFixtures::GRONINGEN));
        $this->setReference('building2', $building);
        $manager->persist($building);

        $building = (new Building())
            ->setName("Brugsmaborg")
            ->setCode("ZP07")
            ->setLocation($this->getReference(LocationFixtures::GRONINGEN));
        $this->setReference('building3', $building);
        $manager->persist($building);

        $building = (new Building())
            ->setName("DSH")
            ->setCode("ZE10")
            ->setLocation($this->getReference(LocationFixtures::GRONINGEN));
        $this->setReference('building4', $building);
        $manager->persist($building);

        $building = (new Building())
            ->setName("Marie Kamphuisborg")
            ->setCode("ZP23")
            ->setLocation($this->getReference(LocationFixtures::GRONINGEN));
        $this->setReference('building5', $building);
        $manager->persist($building);

        $manager->flush();
    }

    public static function getReferenceKey($index)
    {
        return sprintf("building%s", $index);
    }

    public function getDependencies() {
        return array(LocationFixtures::class);
    }
}
