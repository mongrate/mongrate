<?php

namespace Mongrate\Tests;

use Mongrate\Command\TestMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

/**
 * This set of tests runs each migration in the `resources/examples/` directory, and ensures they
 * all pass going up or down.
 */
class EnsureExamplesAllPassTest extends \PHPUnit_Framework_TestCase
{
    private $commandTester;

    public function setUp()
    {
        $yaml = new Parser();
        $parametersFromYmlFile = $yaml->parse(file_get_contents(__DIR__ . '/Command/parameters.yml'))['parameters'];

        $application = new Application();
        $application->add(new TestMigrationCommand(null, $parametersFromYmlFile));

        $command = $application->find('test');
        $commandTester = new CommandTester($command);

        $this->commandTester = $commandTester;
    }

    private function runGoingUp($migrationName)
    {
        $this->runGoingUpOrDown($migrationName, 'up');
    }

    private function runGoingDown($migrationName)
    {
        $this->runGoingUpOrDown($migrationName, 'down');
    }

    private function runGoingUpOrDown($migrationName, $direction)
    {
        $this->commandTester->execute(['command' => 'test', 'name' => $migrationName, 'direction' => $direction]);
        $this->assertEquals(
            "Testing {$migrationName} going {$direction}.\nTest passed.\n",
            $this->commandTester->getDisplay()
        );
    }

    public function testExampleMigrationUpdateAddressStructure()
    {
        $this->runGoingUp('UpdateAddressStructure');
        $this->runGoingDown('UpdateAddressStructure');
    }

    public function testExampleMigrationDeleteOldLogs()
    {
        $this->runGoingUp('DeleteOldLogs');
        // There is no 'down' migration for this example.
    }

    public function testExampleMigrationTemplateImageDimensions()
    {
        $this->runGoingUp('TemplateImageDimensions');
        $this->runGoingDown('TemplateImageDimensions');
    }

    public function testExampleMigrationChangeFieldAndRecordHistory()
    {
        $this->runGoingUp('ChangeFieldAndRecordHistory');
        $this->runGoingDown('ChangeFieldAndRecordHistory');
    }
}
