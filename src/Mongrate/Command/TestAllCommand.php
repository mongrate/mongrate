<?php

namespace Mongrate\Command;

use Mongrate\Exception\InvalidFixturesException;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestAllCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('test-all')
            ->setDescription('Test all migrations up and down.')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migrations = $this->service->getAllMigrations();

        $command = $this->getApplication()->find('test');

        foreach ($migrations as $migration) {
            foreach (['up', 'down'] as $direction) {
                $inputToSingleTestCommand = new ArrayInput([
                    'command' => 'test',
                    'name' => (string) $migration->getName(),
                    'direction' => $direction,
                ]);

                try {
                    $command->run($inputToSingleTestCommand, $output);
                } catch (InvalidFixturesException $e) {
                    $output->writeln('The fixtures for ' . $migration->getName() . ' going ' . $direction . ' are invalid: ' . $e->getMessage());
                }
            }
        }
    }
}
