<?php

namespace Mongrate\Command;

use Mongrate\Exception\DuplicateMigrationName;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMigrationCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('generate-migration')
            ->setDescription('Create a migration class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name, formatted like "UpdateAddressStructure".');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $className = $input->getArgument('name') . '_' . date('Ymd');
        $file = 'migrations/' . $className . '.php';

        if (file_exists($file)) {
            throw new DuplicateMigrationName($className);
        }

        $template = file_get_contents('resources/MigrationTemplate.php');
        $templated = strtr($template, [
            '%class%' => $className
        ]);

        if (!is_dir('migrations')) {
            mkdir('migrations');
        }

        file_put_contents($file, $templated);

        $output->writeln('<info>Generated migration file</info> <comment>' . $file . '</comment>');
    }
}
