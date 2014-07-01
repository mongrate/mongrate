<?php

namespace Mongrate\Command;

use Mongrate\Exception\MigrationDoesntExist;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Migrate up or down')
            ->addArgument('name', InputArgument::REQUIRED, 'The class name, formatted like "20140523_UpdateAddressStructure".');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('name');
        $fullClassName = 'Mongrate\Migrations\\' . $className;
        $file = 'migrations/' . $className . '.php';

        if (!file_exists($file)) {
            throw new MigrationDoesntExist($className);
        }

        $output->writeln('<info>Migrating...</info> <comment>' . $className . '</comment>');
        // @todo - migrate.
    }
}
