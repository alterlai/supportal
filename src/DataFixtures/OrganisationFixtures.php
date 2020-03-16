<?php

namespace App\DataFixtures;

use App\Entity\Organisation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class OrganisationFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $org = new Organisation();
        $org->setName('Hanzehogeschool');
        $org->setColor('#123456');

        $manager->persist($org);
        $manager->flush();
    }
}
