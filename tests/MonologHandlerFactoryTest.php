<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\Notifications\MonologHandlerFactory;

class MonologHandlerFactoryTest extends TestCase
{
    private $factory;

    public function setUp()
    {
        parent::setUp();
        $this->factory = new MonologHandlerFactory();
    }

    public function tearDown()
    {
        unset($this->factory);
    }

    public function testFactoryShouldSuccessfullyCreateAllSupportedDrivers(){
        foreach ($this->supportedDrivers as $driver) {
            $subject = 'Test Subject Line';
            $handler = $this->factory->create($driver,$subject);
            $this->assertNotEmpty($handler);
        }
    }

    public function testFactoryShouldReturnAMonologHandlerInterface()
    {
        $handler = $this->factory->create('slack');
        $this->assertInstanceOf('\Monolog\Handler\HandlerInterface',$handler);
    }
}