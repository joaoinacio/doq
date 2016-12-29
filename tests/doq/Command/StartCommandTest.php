<?php

namespace Tests\doq\Command;

use Tests\doq\Command\ComposeCommandTest;
use Symfony\Component\Console\Tester\CommandTester;
use doq\Application;
use doq\Command\StartCommand;

class StartCommandTest extends ComposeCommandTest
{
    const COMMAND_NAME = 'start';

    public function setUp()
    {
        parent::setUp();
        $this->command = $this->getCommand(self::COMMAND_NAME);
    }

    /**
     * Test that app command is of the correct class
     */
    public function testCommandIsValid()
    {
        $this->assertInstanceOf('doq\Command\StartCommand', $this->command);
    }

    /**
     * Test that issuing the start command will attempt to start the service containers.
     */
    public function testExecuteStartCommand()
    {
        // replace doq\Compose\Command with mock object
        $mockCommand = $this->getMockComposeCommand($this->command);

        $tester = new CommandTester($mockCommand);
        $tester->execute([
            'command' => $mockCommand->getName()
        ]);

        $this->assertRegexp('/Starting docker service containers.../', $tester->getDisplay());
    }

    /**
     * Test that an error will be shown if no config file exists.
     */
    public function testErrorIfNoConfigFile()
    {
        // replace doq\Compose\Command with mock object
        $mockCommand = $this->getMockComposeCommand($this->command);

        $tester = new CommandTester($mockCommand);
        $tester->execute([
            'command' => $mockCommand->getName(),
            '--config' => 'nonexistant'
        ]);

        $this->assertRegexp('/Error/', $tester->getDisplay());
    }
}
