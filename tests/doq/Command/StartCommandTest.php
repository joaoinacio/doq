<?php

namespace Tests\doq\Command;

use Tests\doq\Command\ComposeCommandTest;
use Symfony\Component\Console\Tester\CommandTester;
use doq\Application;
use doq\Command\StartCommand;

class StartCommandTest extends ComposeCommandTest
{
    const COMMAND_NAME = 'start';

    /**
     * Test that app command is of the correct class
     */
    public function testCommandIsValid()
    {
        $this->assertInstanceOf('doq\Command\StartCommand', $this->app->get(self::COMMAND_NAME) );
    }

    /**
     * Test that issuing the start command will attempt to start the service containers.
     */
    public function testExecuteStartCommandDefault()
    {
        $this->mockConfiguration('default');
        $this->mockComposeCommand();

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName()
        ]);

        $this->assertRegexp('/Starting docker service containers.../', $tester->getDisplay());
    }

    /**
     * Test that issuing the start command will attempt to start the service containers.
     */
    public function testExecuteStartCommandNonDefaultConfig()
    {
        $this->mockConfiguration('alternative');
        $this->mockComposeCommand();

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName(),
            '--config' => 'alternative'
        ]);

        $this->assertRegexp('/Starting docker service containers.../', $tester->getDisplay());
    }

    /**
     * Test that an error will be shown if no config file exists.
     */
    public function testErrorIfNoConfigFile()
    {
        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName(),
            '--config' => 'somefile'
        ]);

        $this->assertRegexp('/Error/', $tester->getDisplay());
    }
}
