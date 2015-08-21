<?php

namespace Mongrate\Command;

use Mongrate\Migration\Direction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ToggleMigrationCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this->setName('toggle')
            ->setDescription('Toggle a migration up or down. Useful when writing your migration.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $isAlreadyApplied = $this->isMigrationApplied($this->migrationName);

        if ($isAlreadyApplied === true) {
            $this->migrate(Direction::down());
        } else {
            $this->migrate(Direction::up());
        }
    }
}
