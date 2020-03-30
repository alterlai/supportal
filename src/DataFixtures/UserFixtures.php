<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\RoleRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = (new User())
            ->setUsername("leverancier")
            ->setRole("ROLE_LEVERANCIER")
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setEmail("levr@user.com");
        $user->setPassword($this->passwordEncoder->encodePassword($user, "leverancier"));
        $manager->persist($user);

        $user = (new User())
            ->setUsername("opdrachtgever")
            ->setRole("ROLE_OPDRACHTGEVER")
            ->setPassword($this->passwordEncoder->encodePassword($user, "opdrachtgever"))
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setEmail("opdr@user.com");
        $manager->persist($user);

        $user = (new User())
            ->setUsername("admin")
            ->setRole("ROLE_ADMIN")
            ->setPassword($this->passwordEncoder->encodePassword($user, "admin"))
            ->setEmail("admin@user.com");
        $manager->persist($user);
        $manager->flush();
    }
}
