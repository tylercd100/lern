<?php

namespace Tylercd100\LERN\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tylercd100\LERN\Factories\MonologHandlerFactory;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->supportedDrivers = ['slack','mail','pushover','plivo','twilio','hipchat','flowdock','fleephook'];

        $this->app['config']->set('lern.notify.slack', [
            'token'=>'token',
            'username'=>'username',
            'icon'=>'icon',
            'channel'=>'channel',
        ]);

        $this->app['config']->set('lern.notify.fleephook', [
            'token'=>'token',
        ]);

        $this->app['config']->set('lern.notify.mail', [
            'to'=>'to@address.com',
            'from'=>'from@address.com',
            'smtp'=>true,
        ]);

        $this->app['config']->set('lern.notify.pushover', [
            'token' => 'token',
            'user'  => 'user',
            'sound' => 'siren',
        ]);

        $this->app['config']->set('lern.notify.plivo', [
            'token'   => 'token',
            'auth_id' => 'auth_id',
            'to'      => '+15555555555',
            'from'    => '+16666666666',
        ]);

        $this->app['config']->set('lern.notify.twilio', [
            'secret'  => 'secret',
            'sid'     => 'sid',
            'to'      => '+15555555555',
            'from'    => '+16666666666',
        ]);

        $this->app['config']->set('lern.notify.hipchat', [
            'token' => 'token',
            'room'  => 'room',
            'name'  => 'name',
            'notify'  => false,
        ]);

        $this->app['config']->set('lern.notify.flowdock', [
            'token' => 'token',
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();        
    }

    protected function migrate(){
        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/migrations'),
        ]);
    }

    protected function migrateReset(){
        $this->artisan('migrate:reset');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getPackageProviders($app)
    {
        return ['Tylercd100\LERN\LERNServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('lern.record', [
            'table'=>'vendor_tylercd100_lern_exceptions',
            'collect'=>[
                'method'=>true,//When true it will collect GET, POST, DELETE, PUT, etc...
                'data'=>true,//When true it will collect Input data
                'status_code'=>true,
                'user_id'=>true,
                'url'=>true,
            ],
        ]);

        $app['config']->set('lern.notify', [
            'channel'=>'Tylercd100\LERN',
            'includeExceptionStackTrace'=>true,
            'drivers'=>['pushover'],
            'mail'=>[
                'to'=>'test@mailinator.com',
                'from'=>'from@mailinator.com',
                'smtp'=>true,
            ],
            'pushover'=>[
                'token' => 'token',
                'user'  => 'user',
                'sound'=>'siren',
            ],
            'slack'=>[
                'username'=>'username',
                'icon'=>'icon',
                'channel'=>'channel',
            ]
        ]);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}