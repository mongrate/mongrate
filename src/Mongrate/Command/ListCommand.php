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

        foreach ($iterator as $file) {
            if (substr($file, -4) !== '.php') {
                continue;
            }
            $output->writeln('<comment>' . substr($file, 0, -4) . '</comment>');
        }
    }
}
