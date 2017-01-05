<?php

namespace Tests\doq\Command;

use Tests\doq\Command\ComposeCommandTest;
use Symfony\Component\Console\Tester\CommandTester;

class DestroyCommandTest extends ComposeCommandTest
{
    const COMMAND_NAME = 'destroy';

    /**
     * Test that app command is of the correct class
     */
    public function testCommandIsValid()
    {
        $this->assertInstanceOf('doq\Command\DestroyCommand', $this->app->get(self::COMMAND_NAME) );
    }

    /**
     * Test that issuing the stop command will attempt to stop the service containers.
     */
    public function testExecuteCommandOutput()
    {
        $this->mockConfiguration();
        $this->mockComposeCommand();

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName()
        ]);

        $this->assertRegexp('/Stopping and removing docker containers.../', $tester->getDisplay());
    }

    /**
     * Test that issuing the destroy command will execute docker-compose down command.
     */
    public function testExecuteComposeCommand()
    {
        $this->mockConfiguration();
        $this->mockComposeCommand()
            ->expects($this->once())
            ->method('exec')
            ->with('down');

        $command = $this->app->get(self::COMMAND_NAME);

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName()
        ]);
    }
}
