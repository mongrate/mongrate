<?php

namespace Mongrate\Command;

use Mongrate\Exception\DuplicateMigrationName;
use Mongrate\Model\Name;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMigrationCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('generate-migration')
            ->setDescription('Create a migration class.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name, formatted like "UpdateAddressStructure".');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = new Name($input->getArgument('name') . '_' . date('Ymd'));
        $targetDirectory = dirname($this->service->getMigrationClassFileFromName($name));

        if (is_dir($targetDirectory)) {
            throw new DuplicateMigrationName($name);
        } else {
            mkdir($targetDirectory, 0766, true);
        }

        $iterator = new \DirectoryIterator(__DIR__ . '/../../../resources/migration-template/');

        foreach ($iterator as $file) {
            if ($file->getFileName() === '.' || $file->getFileName() === '..') {
                continue;
            }

            $template = file_get_contents($file->getPathName());
            $templated = strtr($template, [
                '%class%' => $name,
            ]);
            file_put_contents($targetDirectory . '/' . $file->getFileName(), $templated);
        }

        $output->writeln('<info>Generated migration file and YML templates in ' . $targetDirectory  . '</info>');
    }
}
