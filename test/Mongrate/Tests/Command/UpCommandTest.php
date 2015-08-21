<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\UpCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

class UpCommandTest extends BaseCommandTest
{
    public function setUp()
    {
        parent::setUp();

        $collection = $this->db->selectCollection('Company');
        $collection->upsert([], ['name' => 'Amy', 'address' => [
            ['streetFirstLine' => 'Lena Gardens'],
            ['streetFirstLine' => 'Marlow Place'],
        ]]);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add(new UpCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('up');
        $commandTester = new CommandTester($command);
        $collection = $this->db->selectCollection('Company');

        // Starts out with an array of addresses.
        $this->assertCount(2, $collection->findOne(['name' => 'Amy'])['address']);
        $this->assertEquals('Lena Gardens', $collection->findOne(['name' => 'Amy'])['address'][0]['streetFirstLine']);

        // Run the command.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertEquals(
            "Migrating up... UpdateAddressStructure\n"
            ."Migrated up\n",
            $commandTester->getDisplay()
        );

        // Now only has the first address at the root of 'address'.
        $this->assertArrayHasKey('streetFirstLine', $collection->findOne(['name' => 'Amy'])['address']);
        $this->assertEquals('Lena Gardens', $collection->findOne(['name' => 'Amy'])['address']['streetFirstLine']);
    }

    /**
     * @expectedException Mongrate\Exception\MigrationDoesntExist
     * @expectedExceptionMessage There is no migration called "Elvis" in "resources/examples/Elvis/Migration.php"
     */
    public function testExecute_migrationDoesntExist()
    {
        $application = new Application();
        $application->add(new UpCommand(null, $this->parametersFromYmlFile));

        $command = $application->find('up');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'Elvis']);
    }

    public function testExecute_cannotApply()
    {
        $application = new Application();
        $application->add(new UpCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('up');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertContains("Migrated up", $commandTester->getDisplay());

        $this->setExpectedException('Mongrate\Exception\CannotApplyException', 'Cannot go up - the migration is already applied.');
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
    }

    public function testExecute_forceApply()
    {
        $application = new Application();
        $application->add(new UpCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('up');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertContains("Migrated up", $commandTester->getDisplay());

        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure', '--force' => true]);
        $this->assertContains("Migrated up", $commandTester->getDisplay());
    }
}
