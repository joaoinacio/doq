<?php

namespace Tests\doq\Command;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use doq\Application;

abstract class BaseCommandTest extends PHPUnit_Framework_TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = new Application();
    }

}
