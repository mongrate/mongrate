<?php

namespace Mongrate\Command;

use Mongrate\Migration\Name;
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

            $name = new Name($file);

            $migrations[] = [
                'name' => $name,
                'isApplied' => $this->isMigrationApplied($name),
            ];
        }

        usort($migrations, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        foreach ($migrations as $migration) {
            $output->writeln('<comment>' . $migration['name'] . '</comment> '
                . ($migration['isApplied'] ? '<info>applied</info>' : '<error>not applied</error>'));
        }
    }
}
