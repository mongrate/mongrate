<?php

namespace Mongrate\Command;

use FilesystemIterator;
use Mongrate\Exception\CannotApplyException;
use Mongrate\Migration\Direction;
use Mongrate\Migration\Name;
use Mongrate\Model\Migration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command should apply all migrations that are not applied yet.
 */
class UpAllCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this->setName('up-all')
            ->setDescription('Apply any migration that is not applied yet.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->ensureMigrationsDirectoryExists();
        $migrations = $this->service->getMigrationsNotApplied();

        $hasMigrated = false;

        foreach ($migrations as $migration) {
            $this->service->migrate($migration->getName(), Direction::up(), $output);
            $hasMigrated = true;
        }

        if ($hasMigrated) {
            $output->writeln('<info>All migrations are in sync.</info>');
        } else {
            $output->writeln('<info>No migrations to sync. All up to date!</info>');
        }
    }
}
