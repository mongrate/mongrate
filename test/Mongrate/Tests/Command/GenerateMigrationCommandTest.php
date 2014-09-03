<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\GenerateMigrationCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateMigrationCommandTest extends BaseCommandTest
{
    public function setUp()
    {
        parent::setUp();

        $this->expectedFile = 'resources/examples/CreatedByTests_' . date('Ymd') . '/Migration.php';
        $this->duplicateFile = 'resources/examples/DuplicateTest_' . date('Ymd') . '/Migration.php';

        $this->deleteTestFiles();
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->deleteTestFiles();
    }

    private function deleteTestFiles()
    {
        if (is_dir(dirname($this->expectedFile))) {
            exec('rm -fr ' . dirname($this->expectedFile));
        }
        if (is_dir(dirname($this->duplicateFile))) {
            exec('rm -fr ' . dirname($this->duplicateFile));
        }
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add(new GenerateMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('generate-migration');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName(), 'name' => 'CreatedByTests']);
        $this->assertEquals('Generated migration file and YML templates in resources/examples/CreatedByTests_' . date('Ymd') . "\n",
            $commandTester->getDisplay());

        $this->assertFileExists($this->expectedFile);

        $fileContent = file_get_contents($this->expectedFile);
        $this->assertContains('namespace Mongrate\Migrations;' . "\n", $fileContent);
        $this->assertContains('class CreatedByTests_' . date('Ymd') . "\n", $fileContent);
        $this->assertContains('public function up(Database $db)', $fileContent);
        $this->assertContains('public function down(Database $db)', $fileContent);
    }

    public function testExecute_duplicateMigrationName()
    {
        $application = new Application();
        $application->add(new GenerateMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('generate-migration');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName(), 'name' => 'DuplicateTest']);

        $this->setExpectedException('Mongrate\Exception\DuplicateMigrationName', 'A migration with the name "DuplicateTest_');
        $commandTester->execute(['command' => $command->getName(), 'name' => 'DuplicateTest']);

        $this->assertFileNotExists($this->duplicateFile);
    }
}
