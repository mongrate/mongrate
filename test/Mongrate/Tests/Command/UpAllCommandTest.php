<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\UpAllCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class UpAllCommandTest extends BaseCommandTest
{
    private $commandTester;

    private $command;

    public function setUp()
    {
        parent::setUp();

        $application = new Application();
        $application->add(new UpAllCommand(null, $this->parametersFromYmlFile));

        $this->command = $application->find('up-all');
        $this->commandTester = new CommandTester($this->command);
    }

    public function testExecute_hasMigrationsToApply()
    {
        $this->commandTester->execute(['command' => $this->command->getName()]);
        $this->assertEquals(
            "Migrating up... ChangeFieldAndRecordHistory\n"
            ."Migrated up\n"
            ."Migrating up... DeleteOldLogs\n"
            ."Migrated up\n"
            ."Migrating up... TemplateImageDimensions\n"
            ."Migrated up\n"
            ."Migrating up... UpdateAddressStructure\n"
            ."Migrated up\n"
            ."All migrations have been applied.\n",
            $this->commandTester->getDisplay()
        );
    }

    public function testExecute_hasNoMigrationsToApply()
    {
        $this->testExecute_hasMigrationsToApply();

        $this->commandTester->execute(['command' => $this->command->getName()]);
        $this->assertEquals(
            "There are no migrations to apply. They are all already applied.\n",
            $this->commandTester->getDisplay()
        );
    }
}
