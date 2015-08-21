<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\TestMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class TestMigrationCommandTest extends BaseCommandTest
{
    public function testExecute_upAndDown()
    {
        $application = new Application();
        $application->add(new TestMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('test');
        $commandTester = new CommandTester($command);

        // First run should go up.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertEquals(
            "Testing UpdateAddressStructure going up.\n"
            . "Test passed.\n"
            . "Testing UpdateAddressStructure going down.\n"
            . "Test passed.\n",
            $commandTester->getDisplay()
        );
    }

    public function testExecute_up()
    {
        $application = new Application();
        $application->add(new TestMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('test');
        $commandTester = new CommandTester($command);

        // First run should go up.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure', 'direction' => 'up']);
        $this->assertEquals(
            "Testing UpdateAddressStructure going up.\n"
            . "Test passed.\n",
            $commandTester->getDisplay()
        );
    }

    public function testExecute_down()
    {
        $application = new Application();
        $application->add(new TestMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('test');
        $commandTester = new CommandTester($command);

        // First run should go up.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure', 'direction' => 'down']);
        $this->assertEquals(
            "Testing UpdateAddressStructure going down.\n"
            . "Test passed.\n",
            $commandTester->getDisplay()
        );
    }

    /**
     * @expectedException Mongrate\Exception\MigrationDoesntExist
     * @expectedExceptionMessage There is no migration called "Elvis" in "resources/examples/Elvis/Migration.php"
     */
    public function testExecute_migrationDoesntExist()
    {
        $application = new Application();
        $application->add(new TestMigrationCommand(null, $this->parametersFromYmlFile));

        $command = $application->find('test');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'Elvis']);
    }

    public function testExecute_hasMongoObjectsInYml()
    {
        $application = new Application();
        $application->add(new TestMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('test');
        $commandTester = new CommandTester($command);

        // First run should go up.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'DeleteOldLogs', 'direction' => 'up']);
        $this->assertEquals(
            "Testing DeleteOldLogs going up.\n"
            . "Test passed.\n",
            $commandTester->getDisplay()
        );
    }
}
