<?php

namespace Tylercd100\LERN\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tylercd100\LERN\Factories\MonologHandlerFactory;
use Exception;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->supportedDrivers = [
            'slack',
            'mail',
            'pushover',
            'plivo',
            'twilio',
            'hipchat',
            'flowdock',
            'fleephook',
            'mailgun'
        ];
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    protected function migrate()
    {
        $path = "../../../../tests/migrations";
        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--path' => $path,
        ]);
    }

    protected function migrateReset()
    {
        try {
            $version = $this->app->version();
            $version = floatval($version);
        } catch (\Symfony\Component\Debug\Exception\FatalErrorException $e) {
            $version = 0;
        }

        $path = "../../../../tests/migrations";

        if (empty($version) || $version < 5.3) {
            $this->artisan('migrate:reset', [
                '--database' => 'testbench'
            ]);
        } else {
            $this->artisan('migrate:reset', [
                '--database' => 'testbench',
                '--path' => $path,
            ]);
        }
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getPackageProviders($app)
    {
        return [
            \Tylercd100\LERN\LERNServiceProvider::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getPackageAliases($app)
    {
        return [
            \Tylercd100\LERN\Facades\LERN::class,
        ];
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

        $app['config']->set('lern.record', array_merge($app['config']->get('lern.record'), [
            'table'=>'vendor_tylercd100_lern_exceptions',
            'collect'=>[
                'method'=>true,//When true it will collect GET, POST, DELETE, PUT, etc...
                'data'=>true,//When true it will collect Input data
                'status_code'=>true,
                'user_id'=>true,
                'url'=>true,
            ],
            'excludeKeys'=>[
                'password'
            ]
        ]));

        $app['config']->set('lern.notify', array_merge($app['config']->get('lern.notify'), [
            'channel'=>\Tylercd100\LERN::class,
            'includeExceptionStackTrace'=>true,
            'drivers'=>['pushover'],
            'mail'=>[
                'to'=>'test@mailinator.com',
                'from'=>'from@mailinator.com',
                'smtp'=>true,
            ],
            'pushover'=>[
                'token' => 'token',
                'users'  => 'user',
                'sound'=>'siren',
            ],
            'slack'=>[
                'username'=>'username',
                'icon'=>'icon',
                'channel'=>'channel',
            ]
        ]));


        $app['config']->set('lern.notify.view', 'test');

        $app['config']->set('lern.notify.slack', [
            'token' => 'token',
            'username' => 'username',
            'icon' => 'icon',
            'channel' => 'channel',
        ]);

        $app['config']->set('lern.notify.fleephook', [
            'token' => 'token',
        ]);

        $app['config']->set('lern.notify.mail', [
            'to' => 'to@address.com',
            'from' => 'from@address.com',
            'smtp' => true,
        ]);

        $app['config']->set('lern.notify.mailgun', [
            'to'=>'to@address.com',
            'from'=>'from@address.com',
            'token' => 'token',
            'domain' => 'test.com',
        ]);

        $app['config']->set('lern.notify.pushover', [
            'token' => 'token',
            'users'  => 'user',
            'sound' => 'siren',
        ]);

        $app['config']->set('lern.notify.plivo', [
            'token'   => 'token',
            'auth_id' => 'auth_id',
            'to'      => '+15555555555',
            'from'    => '+16666666666',
        ]);

        $app['config']->set('lern.notify.twilio', [
            'secret'  => 'secret',
            'sid'     => 'sid',
            'to'      => '+15555555555',
            'from'    => '+16666666666',
        ]);

        $app['config']->set('lern.notify.hipchat', [
            'token' => 'test-token',
            'room'  => 'test-room',
            'name'  => 'test-name',
            'notify'  => false,
        ]);

        $app['config']->set('lern.notify.flowdock', [
            'token' => 'token',
        ]);


        // Copy stuff
        $root = __DIR__ . "/../vendor/orchestra/testbench/fixture/resources/views";
        if (!is_dir($root)) {
            $root = __DIR__ . "/../vendor/orchestra/testbench-core/laravel/resources/views";
            if(!is_dir($root)) {
                throw new Exception("Could not find laravel inside of testbench. Is testbench installed?");
            }
        }
        
        // Test view
        copy(__DIR__ . "/views/test.blade.php", "{$root}/test.blade.php");

        // Exceptions
        $dir = "{$root}/exceptions";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        copy(__DIR__ . "/../views/exceptions/default.blade.php", $dir . "/default.blade.php");
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