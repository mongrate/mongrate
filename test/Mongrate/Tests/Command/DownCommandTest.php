<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\DownCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

class DownCommandTest extends BaseCommandTest
{
    public function setUp()
    {
        parent::setUp();

        $companies = $this->db->selectCollection('Company');
        $companies->upsert([], ['name' => 'Bob', 'address' => ['streetFirstLine' => 'Lena Gardens']]);

        $migrations = $this->db->selectCollection('MongrateMigrations');
        $migrations->upsert(['className' => 'UpdateAddressStructure'], ['$set' => ['isApplied' => true]]);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add(new DownCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('down');
        $commandTester = new CommandTester($command);
        $collection = $this->db->selectCollection('Company');

        // Starts out with an address at the root of 'address'.
        $this->assertEquals('Lena Gardens', $collection->findOne(['name' => 'Bob'])['address']['streetFirstLine']);

        // Run the command.
        $commandTester->execute(['command' => $command->getName(), 'name' => 'UpdateAddressStructure']);
        $this->assertEquals("Migrating down... UpdateAddressStructure\n"
                ."Migrated down\n",
            $commandTester->getDisplay());

        // Now has an array of addresses at the root of 'address'.
        $this->assertArrayHasKey(0, $collection->findOne(['name' => 'Bob'])['address']);
        $this->assertEquals('Lena Gardens', $collection->findOne(['name' => 'Bob'])['address'][0]['streetFirstLine']);
    }

    /**
     * @expectedException Mongrate\Exception\MigrationDoesntExist
     * @expectedExceptionMessage There is no migration class called "Elvis" in "resources/examples/Elvis.php"
     */
    public function testExecute_migrationDoesntExist()
    {
        $application = new Application();
        $application->add(new DownCommand(null, $this->parametersFromYmlFile));

        $command = $application->find('down');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName(), 'name' => 'Elvis']);
    }
}
