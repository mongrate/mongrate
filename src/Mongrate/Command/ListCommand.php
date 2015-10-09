<?php

namespace Mongrate\Command;

use Mongrate\Model\Name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseCommand
{
    protected function configure()
    {
        // Name cannot be 'list' because that's the name of the Symfony command run if no
        // name is specified.
        $this->setName('list-migrations')
            ->setDescription('List available migrations.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->ensureMigrationsDirectoryExists();
        $migrations = $this->service->getAllMigrations();

        foreach ($migrations as $migration) {
            if ($migration->isApplied()) {
                $output->writeln(sprintf('<comment>%s</comment> <info>applied</info>', $migration->getName()));
            } else {
                $output->writeln(sprintf('<comment>%s</comment> <error>not applied</error>', $migration->getName()));
            }
        }
    }
}
