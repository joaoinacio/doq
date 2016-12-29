<?php

namespace Tests\doq\Compose;

use PHPUnit_Framework_TestCase;
use Exception;

class CommandTest extends PHPUnit_Framework_TestCase
{
    protected $mockCommand;

    public function setUp()
    {
    }

    public function testFailsIfNoConfig()
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
}
