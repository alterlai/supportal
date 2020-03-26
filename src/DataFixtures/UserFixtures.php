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
        $user = new User();
        $user->setUsername("testaccount");
        $user->setEmail("je.van.der.laan@st.hanze.nl");
        $user->setRoles(['ROLE_ADMIN']);
        $user->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE));
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$MDI4UFNneThOZXNVa2FIYg$BDp1NQDiNgY2jtFj4gTSQL3nZILq06q/id/X/2OX9d8'); //1234HvP
        $manager->persist($user);

        $user = (new User())->
            setUsername("user")->
            setRole("ROLE_LEVERANCIER")
            ->setPassword($this->passwordEncoder->encodePassword($user, "user"))
            ->setOrganisation($this->getReference(OrganisationFixtures::ORGANISATIE_REFERENCE))
            ->setEmail("user@user.com");

        $user = (new User())
            ->setUsername("admin")
            ->setRole("ROLE_LEVERANCIER")
            ->setPassword($this->passwordEncoder->encodePassword($user, "admin"))
            ->setEmail("user@user.com");
        $manager->persist($user);
        $manager->flush();
    }
}
