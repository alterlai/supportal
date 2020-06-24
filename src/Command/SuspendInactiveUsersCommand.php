<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SuspendInactiveUsersCommand extends Command
{
    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('iqsupport:suspend_inactive_users')
            ->setDescription('delete inactive users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var User[] $users */
        $users = $this->userRepository->findInactiveUsers();

        foreach ($users as $user )
        {
            $io->note($user->getUsername());
            $user->setSuspended(1);
            $this->em->flush();

        }

        $io->success(sizeof($users).' Users have been suspended.');

        return 0;
    }
}
