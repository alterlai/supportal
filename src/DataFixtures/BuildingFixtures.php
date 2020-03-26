<?php

namespace App\DataFixtures;

use App\Entity\Building;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BuildingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $building = (new Building())
            ->setName("Van Doorenveste")
            ->setCode("ZP11")
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setLocatie("Groningen");
        $manager->persist($building);

        $building2 = (new Building())
            ->setName("Van Olsttoren")
            ->setCode("ZP09")
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setLocatie("Groningen");
        $manager->persist($building2);

        $building3 = (new Building())
            ->setName("Brugsmaborg")
            ->setCode("ZP07")
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setLocatie("Groningen");
        $manager->persist($building3);

        $building4 = (new Building())
            ->setName("DSH")
            ->setCode("ZE10")
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setLocatie("Groningen");
        $manager->persist($building4);

        $building5 = (new Building())
            ->setName("Marie Kamphuisborg")
            ->setCode("ZP23")
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setLocatie("Groningen");
        $manager->persist($building5);

        $manager->flush();
    }

    public function getDependencies() {
        return array(OrganisationFixtures::class);
    }
}
