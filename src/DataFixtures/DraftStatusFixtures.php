<?php

namespace App\DataFixtures;

use App\Entity\DraftStatus;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DraftStatusFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $status = (new DraftStatus())
            ->setName("Geaccepteerd")
            ->setDescription("Succesvol afgerond");
        $manager->persist($status);
        $status = (new DraftStatus())
            ->setName("In behandeling")
            ->setDescription("Het document is in behandeling. U wordt op de hoogte gesteld wanneer dit is voltooid.");
        $manager->persist($status);
        $status = (new DraftStatus())
            ->setName("Afgekeurd")
            ->setDescription("De tekening voldoet niet aan alle eisen.");
        $manager->persist($status);
        $manager->flush();
    }
}
