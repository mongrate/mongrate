<?php

namespace Mongrate\Command;

use Symfony\Component\Console\Input\InputArgument;

class BaseMigrationCommand extends BaseCommand
{
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The class name, formatted like "UpdateAddressStructure_20140523".');
    }
}
