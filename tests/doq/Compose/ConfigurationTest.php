<?php

namespace Tests\doq\Compose;

use PHPUnit_Framework_TestCase;
use doq\Compose\Configuration;
use doq\Compose\Configuration\Exception\ConfigNotFoundException;
use doq\Compose\Configuration\Exception\ConfigExistsException;
use org\bovigo\vfs\vfsStream;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Mock doq\Compose\Configuration with a vfs file
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function mockConfiguration($configName = 'default')
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
        return $mockComposeConfig;
    }

    public function testAssertFileExists()
    {
        $config = new Configuration('test');

        try {
            $config->assertFileExists();
            $this->fail("Expected exception was not thrown");
        } catch (ConfigNotFoundException $e) {
            $this->assertEquals("Configuration file '.docker-compose/test.yml' does not exist.", $e->getMessage());
        }
    }

    public function testAssertFileDoesNotExist()
    {
        $mockConfiguration = $this->mockConfiguration('test');

        try {
            $mockConfiguration->assertFileDoesNotExist();
            $this->fail("Expected exception was not thrown");
        } catch (ConfigExistsException $e) {
            $this->assertRegexp('/Configuration file \'(vfs:\/\/tests\/).docker-compose\/test.yml\' already exists./', $e->getMessage());
        }
    }
}
