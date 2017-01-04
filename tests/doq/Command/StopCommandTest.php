<?php

namespace Tests\doq\Command;

use Tests\doq\Command\ComposeCommandTest;
use Symfony\Component\Console\Tester\CommandTester;
use doq\Application;

class StopCommandTest extends ComposeCommandTest
{
    const COMMAND_NAME = 'stop';

    /**
     * Test that app command is of the correct class
     */
    public function testCommandIsValid()
    {
        $this->assertInstanceOf('doq\Command\StopCommand', $this->app->get(self::COMMAND_NAME) );
    }

    /**
     * Test that issuing the stop command will attempt to stop the service containers.
     */
    public function testExecuteStopCommandDefault()
    {
        $this->mockConfiguration('default');
        $this->mockComposeCommand();

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName()
        ]);

        $this->assertRegexp('/Stopping docker service containers.../', $tester->getDisplay());
    }
}
