<?php

namespace Mongrate\Command;

use Mongrate\Exception\CannotApplyException;
use Mongrate\Migration\Direction;
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
        parent::execute($input, $output);

        $isAlreadyApplied = $this->service->isMigrationApplied($this->migrationName);

        $force = $input->getOption('force');

        if ($isAlreadyApplied === true && !$force) {
            throw new CannotApplyException('Cannot go up - the migration is already applied.');
        } else {
            $this->migrate(Direction::up());
        }
    }
}
