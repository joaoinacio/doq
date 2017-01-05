<?php

namespace Tests\doq\Command;

use Tests\doq\Command\ComposeCommandTest;
use Symfony\Component\Console\Tester\CommandTester;

class LogsCommandTest extends ComposeCommandTest
{
    const COMMAND_NAME = 'logs';

    /**
     * Test that app command is of the correct class
     */
    public function testCommandIsValid()
    {
        $this->assertInstanceOf('doq\Command\LogsCommand', $this->app->get(self::COMMAND_NAME) );
    }

    /**
     * Test that issuing the 'logs' command will attempt to call docker-compose
     */
    public function testExecuteLogsCommand()
    {
        $this->mockConfiguration();
        $this->mockComposeCommand()
            ->expects($this->once())
            ->method('exec')
            ->with('logs');

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName()
        ]);
    }

    /**
     * Test that calling the command with a service parameter will pass
     * the correct parameters to docker-compose.
     */
    public function testExecuteLogsCommandWithServiceArg()
    {
        $this->mockConfiguration();
        $this->mockComposeCommand()
            ->expects($this->once())
            ->method('exec')
            ->with('logs', $this->anything(), ['test']);

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName(),
            'service' => 'test'
        ]);
    }
}
