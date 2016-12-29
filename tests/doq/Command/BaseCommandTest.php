<?php

namespace Tests\doq\Command;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use doq\Application;

abstract class BaseCommandTest extends PHPUnit_Framework_TestCase
{
    protected $app;

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
}
