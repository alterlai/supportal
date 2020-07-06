<?php

namespace App\DataFixtures;

use App\Entity\Organisation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class OrganisationFixtures extends Fixture
{

    public const ORGANISATIE_REFERENCE = "Hanzehogeschool";

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $org = new Organisation();
        $org->setName('Hanzehogeschool');
        $org->setColor('#123456');
        $org->setLogoFileName("");
        $org->setUpdatedAt(new \DateTime("now"));
        $this->addReference(self::ORGANISATIE_REFERENCE, $org);

        $manager->persist($org);
        $manager->flush();
    }
}
