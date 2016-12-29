<?php

namespace Tests\doq\Command;

use Tests\doq\Command\BaseCommandTest;
use Symfony\Component\Console\Tester\CommandTester;
use doq\Application;
use doq\Command\StartCommand;

abstract class ComposeCommandTest extends BaseCommandTest
{
    /**
     * Create a mock command that does not actually execute docker-compose.
     * Replaces the command instance in the app.
     *
     * @param doq\Command\ComposeCommand $command
     */
    public function getMockComposeCommand($command)
    {
        // Create a stub for the DockerCompose class.
        $mockComposeCommand = $this
            ->getMockBuilder('doq\Compose\Command')
            ->setMethods(['exec'])
            ->getMock();
        $mockComposeCommand
            ->expects($this->any())
            ->method('exec');

        // have the command use the mocked instance
        $mockCommand = $this
            ->getMockBuilder(get_class($command))
            ->setMethods(['getDockerComposeCommand'])
            ->getMock();
        $mockCommand->expects($this->once())
            ->method('getDockerComposeCommand')
            ->will($this->returnValue($mockComposeCommand));

        // replace the command on application
        //$this->app->add($command);
        return $mockCommand;
    }
}
