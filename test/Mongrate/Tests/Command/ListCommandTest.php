<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\ListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

class ListCommandTest extends \PHPUnit_Framework_TestCase
{
    private $parametersFromYmlFile;

    public function setUp()
    {
        $yaml = new Parser();
        $this->parametersFromYmlFile = $yaml->parse(file_get_contents(__DIR__ . '/parameters.yml'))['parameters'];
    }

    public function tearDown()
    {
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add(new ListCommand(null, $this->parametersFromYmlFile));

        $command = $application->find('list-migrations');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals("UpdateAddressStructure\n", $commandTester->getDisplay());
    }
}
