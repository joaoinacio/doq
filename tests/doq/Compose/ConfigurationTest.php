<?php

namespace Tests\doq\Compose;

use PHPUnit_Framework_TestCase;
use doq\Compose\Configuration;
use Exception;
use doq\Compose\Configuration\Exception\ConfigNotFoundException;
use doq\Compose\Configuration\Exception\ConfigExistsException;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    protected $mockCommand;

    public function setUp()
    {
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
}
