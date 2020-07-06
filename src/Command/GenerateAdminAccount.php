<?php

namespace App\Command;

use App\Entity\DocumentType;
use App\Entity\Organisation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Date;

class GenerateAdminAccount extends Command
{
    private $em;
    private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->encoder = $encoder;
    }

    protected function configure()
    {
        $this
            ->setName('iqsupport:generate:admin')
            ->setDescription('Generate an admin account with a random password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $admin = new User();
        try {
            $password = random_int(10000000, 100000000);

            $org = new Organisation();
            $org->setName("IQSupport")
                ->setColor("#000000")
                ->setLogoFileName("")
                ->setUpdatedAt(new \DateTime("now"));

            $this->em->persist($org);

            $admin->setRole("ROLE_ADMIN")
                ->setPassword($this->encoder->encodePassword($admin, $password))
                ->setOrganisation($org)
                ->setSuspended(0)
                ->setUsername("admin")
                ->setEmail("no-reply@iqsupportbv.nl");

            $this->em->persist($admin);

            $this->em->flush();

            $io->note("Password: ".$password);

            $io->success("Admin created!");

        } catch (\Exception $e) {
            $io->error("Error generating password");
            $io->error($e);
        }

        return 0;
    }
}