<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\TestMigrationCommand;
use Mongrate\Command\TestAllCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TestAllCommandTest extends BaseCommandTest
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new TestAllCommand(null, $this->parametersFromYmlFile));
        $application->add(new TestMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('test-all');

        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $display = $commandTester->getDisplay();
        $numberOfLines = substr_count($display, "\n");
        $this->assertGreaterThan(10, $numberOfLines, 'There should be many lines indicating every example has been tested');
    }
}
