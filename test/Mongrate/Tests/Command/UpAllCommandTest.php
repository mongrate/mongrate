<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\UpAllCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class UpAllCommandTest extends BaseCommandTest
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add(new UpAllCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('up-all');
        $commandTester = new CommandTester($command);

        // Run the command.
        $commandTester->execute(['command' => $command->getName()]);
        $this->assertEquals(
            "Migrating up... ChangeFieldAndRecordHistory\n"
            ."Migrated up\n"
            ."Migrating up... DeleteOldLogs\n"
            ."Migrated up\n"
            ."Migrating up... TemplateImageDimensions\n"
            ."Migrated up\n"
            ."Migrating up... UpdateAddressStructure\n"
            ."Migrated up\n"
            ."All migrations are in sync.\n",
            $commandTester->getDisplay()
        );
    }
}
