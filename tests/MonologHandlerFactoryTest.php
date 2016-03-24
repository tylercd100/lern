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

        $this->app['config']->set('lern.notify.slack', [
            'token'=>'token',
            'username'=>'username',
            'icon'=>'icon',
            'channel'=>'channel',
        ]);

        $this->app['config']->set('lern.notify.mail', [
            'to'=>'to@address.com',
            'from'=>'from@address.com',
        ]);

        $this->app['config']->set('lern.notify.pushover', [
            'token' => 'token',
            'user'  => 'user',
            'sound'=>'siren',
        ]);
    }

    public function tearDown()
    {
        unset($this->factory);
    }

    public function testFactoryShouldSuccessfullyCreateAllSupportedDrivers(){
        foreach (['slack','mail','pushover'] as $driver) {
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