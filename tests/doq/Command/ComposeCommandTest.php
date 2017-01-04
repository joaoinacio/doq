<?php

namespace Tests\doq\Command;

use Tests\doq\Command\BaseCommandTest;
use org\bovigo\vfs\vfsStream;

use Symfony\Component\Console\Tester\CommandTester;
use doq\Application;
use doq\Command\StartCommand;

abstract class ComposeCommandTest extends BaseCommandTest
{
    /**
     * @var vfsStream
     */
    protected $vfsroot;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Mock compose command to not execute docker-compose
     */
    protected function mockComposeCommand()
    {
        $mockComposeCommand = $this->getMockBuilder('doq\Compose\Command')
            ->disableOriginalConstructor()
            ->setMethods(['exec'])
            ->getMock();
        $mockComposeCommand
            ->expects($this->any())
            ->method('exec');

        $mockComposeCommand->setConfiguration(
            $this->app->getContainer()->get('doq.compose.configuration')
        );

        $this->app->getContainer()->set('doq.compose.command', $mockComposeCommand);

        return $mockComposeCommand;
    }

    /**
     * Mock configuration/filesystem using vfsStream
     */
    protected function mockConfiguration($configName = 'default')
    {
        $vfsroot = vfsStream::setup('tests', null, [
            '.docker-compose' => [
                $configName. '.yml' => 'version: 2.1'
            ],
        ]);

        $mockComposeConfig =  $this->getMockBuilder('doq\Compose\Configuration')
            ->disableOriginalConstructor()
            ->setMethods(['getConfigFilePath'])
            ->getMock();
        $mockComposeConfig
            ->expects($this->any())
            ->method('getConfigFilePath')
            ->will(
                $this->returnCallback(function($fileName) use ($vfsroot) {
                    return $vfsroot->url() . DIRECTORY_SEPARATOR . sprintf('%s/%s.yml', '.docker-compose', $fileName);
                })
            );
        // call constructor
        $mockComposeConfig->__construct($configName);

        // set the mocked object in the container
        $this->app->getContainer()->set('doq.compose.configuration', $mockComposeConfig);

        return $mockComposeConfig;
    }

}
