<?php

namespace Mongrate\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends BaseCommand
{
    protected function configure()
    {
        // Name cannot be 'list' because that's the name of the Symfony command run if no
        // name is specified.
        $this->setName('list-migrations')
            ->setDescription('List available migrations.');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $iterator = new \DirectoryIterator($this->params['migrations_directory']);
        $migrations = [];

        foreach ($iterator as $file) {
            $file = (string) $file;
            if ($file === '.'|| $file === '..') {
                continue;
            }

            $migrations[] = ['file' => $file, 'isApplied' => $this->isMigrationApplied($file)];
        }

        usort($migrations, function($a, $b) {
            return strcmp($a['file'], $b['file']);
        });

        foreach ($migrations as $migration) {
            $output->writeln('<comment>' . $migration['file'] . '</comment> '
                . ($migration['isApplied'] ? '<info>applied</info>' : '<error>not applied</error>'));
        }
    }
}
