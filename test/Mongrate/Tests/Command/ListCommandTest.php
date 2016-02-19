<?php

namespace Mongrate\Tests\Command;

use Mongrate\Command\ListCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

class ListCommandTest extends BaseCommandTest
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new ListCommand(null, $this->parametersFromYmlFile));

        $command = $application->find('list-migrations');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals("ChangeFieldAndRecordHistory not applied\nDeleteOldLogs not applied\nRemoveBrokenReference not applied\nTemplateImageDimensions not applied\nUpdateAddressStructure not applied\n",
            $commandTester->getDisplay());
    }
}
