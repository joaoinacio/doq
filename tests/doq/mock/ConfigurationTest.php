<?php

namespace Tests\doq\mock;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;

class ConfigurationTest
{
    protected static $testCase;

    protected static $vfsroot;

    public static function setTestCase(PHPUnit_Framework_TestCase $testCase)
    {
        self::$testCase = $testCase;
    }

    /**
     * Mock doq\Compose\Configuration with a vfs file system
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public static function getMock($configName = 'default', $methods=[])
    {
        $vfsroot = vfsStream::setup('tests', null, [
            '.docker-compose' => [
                $configName. '.yml' => 'version: 2.1'
            ],
        ]);
        self::$vfsroot = $vfsroot;

        $mockComposeConfig = self::$testCase->getMockBuilder('doq\Compose\Configuration')
            ->disableOriginalConstructor()
            ->setMethods(array_merge($methods, ['getConfigFilePath']))
            ->getMock();
        $mockComposeConfig
            ->expects(self::$testCase->any())
            ->method('getConfigFilePath')
            ->will(
                self::$testCase->returnCallback(function($fileName) use ($vfsroot) {
                    return $vfsroot->url() . DIRECTORY_SEPARATOR . sprintf('%s/%s.yml', '.docker-compose', $fileName);
                })
            );
        // call constructor
        $mockComposeConfig->__construct($configName);
        return $mockComposeConfig;
    }

    public static function getVfs()
    {
        return self::$vfsroot;
    }
}
