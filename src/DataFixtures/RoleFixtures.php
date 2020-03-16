<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public const LEVERANCIER_REFERENCE = "Leverancier";
    public const OPDRACHTGEVER_REFERENCE = "Opdrachtgever";

    public function load(ObjectManager $manager)
    {
        $role = new Role();
        $role->setName('LVRC');
        $role->setDescription('Leveranciers');
        $this->addReference(self::LEVERANCIER_REFERENCE, $role);
        $manager->persist($role);
        $role2 = new Role();
        $role2->setName('OPDR');
        $role2->setDescription('Opdrachtgever');
        $this->addReference(self::OPDRACHTGEVER_REFERENCE, $role2);
        $manager->persist($role2);

        $manager->flush();
    }
}
