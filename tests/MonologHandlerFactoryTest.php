<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\Notifications\MonologHandlerFactory;

class MonologHandlerFactoryTest extends TestCase
{
    private $factoryInstance;

    public function setUp()
    {
        parent::setUp();
        $this->factoryInstance = new MonologHandlerFactory();
    }

    public function tearDown()
    {
        unset($this->factoryInstance);
        parent::tearDown();        
    }

    public function testFactoryShouldSuccessfullyCreateAllSupportedDrivers(){
        foreach ($this->supportedDrivers as $driver) {
            $subject = 'Test Subject Line';
            $handler = $this->factoryInstance->create($driver,$subject);
            $this->assertNotEmpty($handler);
        }
    }

    public function testFactoryShouldReturnAMonologHandlerInterface()
    {
        foreach ($this->supportedDrivers as $driver) {
            $subject = 'Test Subject Line';
            $handler = $this->factoryInstance->create($driver,$subject);
            $this->assertInstanceOf('\Monolog\Handler\HandlerInterface',$handler);
        }
    }
}