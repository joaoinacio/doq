<?php

namespace Tests\doq\Command\StartCommandTest;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use doq\Application;
use doq\Command\StartCommand;

class StartCommandTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->app = new Application();
    }

    protected function getCommand($name)
    {
        try {
            $command = $this->app->get($name);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        return $command;
    }


    public function testCommandExists()
    {
        $command = $this->getCommand('start');
        $this->assertInstanceOf('doq\Command\StartCommand', $command);
    }

    /**
     * @depends testCommandExists
     */
    public function testExecuteStartCommand()
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
        $command = $this
            ->getMockBuilder('doq\Command\StartCommand')
            ->setMethods(['getDockerComposeCommand'])
            ->getMock();
        $command->expects($this->once())
            ->method('getDockerComposeCommand')
            ->will($this->returnValue($mockComposeCommand));

        // replace the command on application
        $this->app->add($command);

        $command = $this->getCommand('start');

        $tester = new CommandTester($command);
        $tester->execute(
            ['command'      => $command->getName()]
        );

        $this->assertRegexp('/Starting docker service containers.../', $tester->getDisplay());
    }

    /**
     * @depends testCommandExists
     */
    public function testErrorIfNoConfigFile()
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
        $command = $this
            ->getMockBuilder('doq\Command\StartCommand')
            ->setMethods(['getDockerComposeCommand'])
            ->getMock();
        $command->expects($this->once())
            ->method('getDockerComposeCommand')
            ->will($this->returnValue($mockComposeCommand));

        // replace the command on application
        $this->app->add($command);

        $command = $this->getCommand('start');

        $tester = new CommandTester($command);
        $tester->execute([
            'command' => $command->getName(),
            '--config' => 'nonexistant'
        ]);

        $this->assertRegexp('/Error/', $tester->getDisplay());
    }
}
