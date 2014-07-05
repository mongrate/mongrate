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

        $this->expectedFile = 'resources/examples/CreatedByTests_' . date('Ymd') . '.php';

        if (file_exists($this->expectedFile)) {
            unlink($this->expectedFile);
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->expectedFile)) {
            unlink($this->expectedFile);
        }
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add(new GenerateMigrationCommand(null, $this->parametersFromYmlFile));
        $command = $application->find('generate-migration');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName(), 'name' => 'CreatedByTests']);
        $this->assertEquals('Generated migration file ' . $this->expectedFile . "\n",
            $commandTester->getDisplay());

        $this->assertFileExists($this->expectedFile);

        $fileContent = file_get_contents($this->expectedFile);
        $this->assertContains('namespace Mongrate\Migrations;' . "\n", $fileContent);
        $this->assertContains('class CreatedByTests_20140705' . "\n", $fileContent);
        $this->assertContains('public function up(Database $db)', $fileContent);
        $this->assertContains('public function down(Database $db)', $fileContent);
    }
}
