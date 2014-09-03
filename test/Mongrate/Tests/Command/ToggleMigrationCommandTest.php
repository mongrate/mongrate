<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\ToggleMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ToggleMigrationCommandTest extends BaseCommandTest
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new ToggleMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('toggle');
        $commandTester = new CommandTester($command);

        // Should start with no record of whether the migration has been applied or not.
        $migrationsCollection = $this->db->selectCollection('MongrateMigrations');
        $this->assertNull($migrationsCollection->findOne(['className' => 'UpdateAddressStructure']));

        // First run should go up.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertEquals("Migrating up... UpdateAddressStructure\n"
                ."Migrated up\n",
            $commandTester->getDisplay());
        $this->assertTrue($migrationsCollection->findOne(['className' => 'UpdateAddressStructure'])['isApplied']);

        // Then down.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertEquals("Migrating down... UpdateAddressStructure\n"
                ."Migrated down\n",
            $commandTester->getDisplay());
        $this->assertFalse($migrationsCollection->findOne(['className' => 'UpdateAddressStructure'])['isApplied']);

        // Then up again.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertEquals("Migrating up... UpdateAddressStructure\n"
                ."Migrated up\n",
            $commandTester->getDisplay());
        $this->assertTrue($migrationsCollection->findOne(['className' => 'UpdateAddressStructure'])['isApplied']);
    }

    /**
     * @expectedException Mongrate\Exception\MigrationDoesntExist
     * @expectedExceptionMessage There is no migration class called "Elvis" in "resources/examples/Elvis/Migration.php"
     */
    public function testExecute_migrationDoesntExist()
    {
        $application = new Application();
        $application->add(new ToggleMigrationCommand(null, $this->parametersFromYmlFile));

        $command = $application->find('toggle');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'Elvis']);
    }
}
