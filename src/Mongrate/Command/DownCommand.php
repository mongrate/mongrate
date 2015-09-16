<?php

namespace Mongrate\Command;

use Mongrate\Exception\CannotApplyException;
use Mongrate\Migration\Direction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DownCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this->setName('down')
            ->setDescription('Revert your migration - execute the `down` method of your migration.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Force going down, even if the migration has not been applied yet.',
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

        if ($isAlreadyApplied === false && !$force) {
            throw new CannotApplyException('Cannot go down - the migration is not applied yet.');
        } else {
            $this->migrate(Direction::down());
        }
    }
}
