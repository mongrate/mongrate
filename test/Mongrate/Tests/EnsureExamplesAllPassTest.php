<?php

namespace Mongrate\Tests;

use Mongrate\Command\TestMigrationCommand;
use Mongrate\Enum\DirectionEnum;
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

    private function runGoingUp($migrationName, $expectedLogOutput = null)
    {
        $this->runGoingUpOrDown($migrationName, DirectionEnum::UP, $expectedLogOutput);
    }

    private function runGoingDown($migrationName, $expectedLogOutput = null)
    {
        $this->runGoingUpOrDown($migrationName, DirectionEnum::DOWN, $expectedLogOutput);
    }

    private function runGoingUpOrDown($migrationName, $direction, $expectedLogOutput)
    {
        $this->commandTester->execute(['command' => 'test', 'name' => $migrationName, 'direction' => $direction]);
        $this->assertEquals(
            "Testing {$migrationName} going {$direction}.\n" . $expectedLogOutput . "Test passed.\n",
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
        $this->runGoingUp(
            'TemplateImageDimensions',
            "Got dimensions for image http://www.wearetwogether.com/images/icons/client-logo-active-brocade.png: 92x33\nGot dimensions for image http://www.wearetwogether.com/images/icons/client-logo-active-intuit.png: 69x33\n"
        );
        $this->runGoingDown('TemplateImageDimensions');
    }

    public function testExampleMigrationChangeFieldAndRecordHistory()
    {
        $this->runGoingUp('ChangeFieldAndRecordHistory');
        $this->runGoingDown('ChangeFieldAndRecordHistory');
    }
}
