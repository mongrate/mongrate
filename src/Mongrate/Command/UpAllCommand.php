<?php

namespace Mongrate\Command;

use Mongrate\Migration\Direction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command will apply all migrations that are not applied yet.
 */
class UpAllCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('up-all')
            ->setDescription('Apply any migration that is not applied yet.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->ensureMigrationsDirectoryExists();

        $migrationsNotApplied = $this->service->getMigrationsNotApplied();

        if (count($migrationsNotApplied) === 0) {
            $output->writeln('<info>There are no migrations to apply. They are all already applied.</info>');
            return;
        }

        foreach ($migrationsNotApplied as $migration) {
            $this->service->migrate($migration->getName(), Direction::up(), $output);
            $hasMigrated = true;
        }

        $output->writeln('<info>All migrations have been applied.</info>');
    }
}
