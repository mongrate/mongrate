<?php

namespace Mongrate\Command;

use Mongrate\Exception\CannotApplyException;
use Mongrate\Migration\Direction;
use Mongrate\Migration\Name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this->setName('up')
            ->setDescription('Apply your migration - execute the `up` method of your migration.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Force going up, even if the migration has already been applied.',
                false
            )
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = new Name($input->getArgument('name'));
        $force = $input->getOption('force');

        $isAlreadyApplied = $this->service->isMigrationApplied($name);

        if ($isAlreadyApplied === true && !$force) {
            throw new CannotApplyException('Cannot go up - the migration is already applied.');
        } else {
            $this->service->migrate($name, Direction::up(), $output);
        }
    }
}
