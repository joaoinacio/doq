<?php

namespace Tests\doq\Command;

use Tests\doq\Command\BaseCommandTest;
use Tests\doq\mock\ConfigurationTest as MockConfiguration;

abstract class ComposeCommandTest extends BaseCommandTest
{
    /**
     * @var vfsStream
     */
    protected $vfsroot;

    public function setUp()
    {
        parent::setUp();
        MockConfiguration::setTestCase($this);
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
        $mockComposeConfig = MockConfiguration::getMock($configName);

        // set the mocked object in the container
        $this->app->getContainer()->set('doq.compose.configuration', $mockComposeConfig);

        return $mockComposeConfig;
    }

}
