<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('iqsupport:install')
            ->setDescription('Install necessary data in database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $commands = [
            $this->getApplication()->find('iqsupport:import:disciplines'),
            $this->getApplication()->find('iqsupport:import:documentTypes'),
            $this->getApplication()->find('iqsupport:import:draftstatus'),
            $this->getApplication()->find('iqsupport:generate:admin'),
        ];

        foreach ($commands as $command) {
            try {
                $command->run($input, $output);
            }
            catch (\Exception $e)
            {
                $io->error("Error executing command ");
                $io->error($e);
            }
        }

        return 0;
    }
}