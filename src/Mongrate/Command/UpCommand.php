<?php

namespace Mongrate\Command;

use Mongrate\Exception\CannotApplyException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends BaseMigrationCommand
{
    protected function configure()
    {
        $this->setName('up');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $isAlreadyApplied = $this->isMigrationApplied($this->className);

        if ($isAlreadyApplied === true) {
            throw new CannotApplyException('Cannot go up - the migration is already applied.');
        } else {
            $this->migrate('up');
        }
    }
}
