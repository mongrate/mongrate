<?php

namespace Mongrate\Command;

use Mongrate\Migration\Direction;
use Mongrate\Migration\Name;
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
        $name = new Name($input->getArgument('name'));

        $isAlreadyApplied = $this->service->isMigrationApplied($name);

        if ($isAlreadyApplied === true) {
            $this->service->migrate($name, Direction::down(), $output);
        } else {
            $this->service->migrate($name, Direction::up(), $output);
        }
    }
}
