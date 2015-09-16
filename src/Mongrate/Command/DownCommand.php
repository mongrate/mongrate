<?php

namespace Mongrate\Command;

use Mongrate\Exception\CannotApplyException;
use Mongrate\Migration\Direction;
use Mongrate\Migration\Name;
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
        $name = new Name($input->getArgument('name'));
        $force = $input->getOption('force');

        $isAlreadyApplied = $this->service->isMigrationApplied($name);

        if ($isAlreadyApplied === false && !$force) {
            throw new CannotApplyException('Cannot go down - the migration is not applied yet.');
        } else {
            $this->service->migrate($name, Direction::down(), $output);
        }
    }
}
