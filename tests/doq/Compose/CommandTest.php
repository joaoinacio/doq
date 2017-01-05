<?php

namespace Tests\doq\Compose;

use PHPUnit_Framework_TestCase;
use Exception;
use Tests\doq\mock\ConfigurationTest as MockConfiguration;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function testFailsIfNoConfigProvided()
    {
        // Create a mock.
        $mockCommand = $this
            ->getMockBuilder('doq\Compose\Command')
            ->setMethods(['exec'])
            ->getMock();

        try {
            $mockCommand->execute('ps');
            $this->fail("Expected exception was not thrown");
        } catch (Exception $e) {
            $this->assertEquals('docker-compose configuration was not provided', $e->getMessage());
        }
    }

    public function testExecuteCommand()
    {
        // Create a mock.
        $mockCommand = $this
            ->getMockBuilder('doq\Compose\Command')
            ->setMethods(['exec', 'getResult'])
            ->getMock();

        $mockCommand->expects($this->once())
            ->method('exec')
            ->with('ps');

        $mockCommand->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue(0));

        $mockCommand->setConfiguration(MockConfiguration::getMock('default'));

        $mockCommand->execute('ps');
        $this->assertEquals(0, $mockCommand->getResult());
    }

    public function testTempFileIsRemoved()
    {
        // create compose configuration mock
        $mockComposeConfig = MockConfiguration::getMock('default', ['newTemporaryFileName']);

        // mock new temp file in vfs
        $mockComposeConfig->expects($this->once())
            ->method('newTemporaryFileName')
            ->will(
                $this->returnCallback(function() {
                    $tempFileName = 'vfs://tests/compose-temp';
                    touch($tempFileName);
                    return $tempFileName;
                })
            );

        // Create compose command mock.
        $mockCommand = $this
            ->getMockBuilder('doq\Compose\Command')
            ->setMethods(['exec', 'getResult'])
            ->getMock();

        $mockCommand->expects($this->once())
            ->method('exec')
            ->with('ps');
        $mockCommand->expects($this->once())
            ->method('getResult')
            ->will($this->returnCallback(function() {
                $this->assertFalse( file_exists('vfs://tests/compose-temp'), 'Temporary configuration file was not removed.' );
                return 0;
            }));

        $mockCommand->setConfiguration($mockComposeConfig);
        $mockCommand->execute('ps');
    }
}
