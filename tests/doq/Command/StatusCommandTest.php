<?php

namespace Tests\doq\Command;

use Tests\doq\Command\ComposeCommandTest;
use Symfony\Component\Console\Tester\CommandTester;

class StatusCommandTest extends ComposeCommandTest
{
    const COMMAND_NAME = 'status';

    /**
     * Test that app command is of the correct class
     */
    public function testCommandIsValid()
    {
        $this->assertInstanceOf('doq\Command\StatusCommand', $this->app->get(self::COMMAND_NAME) );
    }

    /**
     * Test that issuing the status command will attempt to call docker-compose
     */
    public function testExecuteStatusCommand()
    {
        $this->mockConfiguration();
        $this->mockComposeCommand()
            ->expects($this->once())
            ->method('exec')
            ->with('ps');

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName()
        ]);

    }
}
