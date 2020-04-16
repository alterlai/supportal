<?php

namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Organisation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    public const GRONINGEN = "Groningen";

    public function load(ObjectManager $manager)
    {
        $loc = (new Location())
            ->setName("Groningen")
            ->setCode("GRO")
            ->setOrganisation( $this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE));
        $manager->persist($loc);
        $this->addReference(LocationFixtures::GRONINGEN, $loc);

//        $loc = (new Location())
//            ->setName("Assen")
//            ->setCode("ASS")
//            ->setOrganisationId( $this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE));
//        $manager->persist($loc);
//
//        $loc = (new Location())
//            ->setName("Amsterdam")
//            ->setCode("AMS")
//            ->setOrganisationId( $this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE));
//        $manager->persist($loc);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(OrganisationFixtures::class);
    }
}