<?php

namespace Tylercd100\LERN\Tests;

use Illuminate\Support\Facades\Request;
use Tylercd100\LERN\Components\Recorder;
use Tylercd100\LERN\Exceptions\RecorderFailedException;
use Tylercd100\LERN\Exceptions\NotifierFailedException;
use Throwable;

class RecorderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->migrate();
    }

    public function tearDown(): void
    {
        $this->migrateReset();
        parent::tearDown();
    }

    public function testExcludeKeysRemovesTheCorrectValues()
    {

        $data = [
            'email'=>'mail@test.com',
            'password'=>'foobar',
            'name'=>'Foo Bar'
        ];
        $this->app['config']->set('lern.record.excludeKeys', ['password','email']);
        $recorder = new Recorder;
        $result = $this->invokeMethod($recorder, 'excludeKeys', [$data]);
        $this->assertArrayNotHasKey('password', $result);
        $this->assertArrayNotHasKey('email', $result);
    }

    public function testExcludeKeysRemovesNestedValues()
    {
        $data = ['user'=>['email','mail@test.com','password'=>'foobar','name'=>'Foo Bar'],'status'=>200];
        $this->app['config']->set('lern.record.excludeKeys', ['password','email']);
        $recorder = new Recorder;
        $result = $this->invokeMethod($recorder, 'excludeKeys', [$data]);
        $this->assertArrayNotHasKey('password', $result['user']);
        $this->assertArrayNotHasKey('email', $result['user']);
    }

    public function testCollectMethodReturnsFalseWhenConfigValuesAreFalse()
    {
        $this->app['config']->set('lern.record.collect', [
            'method'=>false,
            'data'=>false,
            'status_code'=>false,
            'user_id'=>false,
            'url'=>false
        ]);
        $recorder = new Recorder;
        $model = $recorder->record(new Throwable);
        $this->assertEquals($model->url, null);
    }

    public function testItThrowsRecorderFailedExceptionWhenExceptionIsThrown()
    {
        $mock = $this->createMock(Recorder::class, ['record']);

        $mock->expects($this->once())
                 ->method('record')
                 ->will($this->throwException(new RecorderFailedException));

        $this->expectException(RecorderFailedException::class);

        $mock->record(new Throwable);
    }

    public function testRecordShouldReturnFalseWhenPassedRecorderFailedException()
    {
        $recorder = new Recorder;
        $result = $recorder->record(new RecorderFailedException);
        $this->assertEquals(false, $result);
    }

    public function testRecordShouldReturnTrueWhenPassedNotifierFailedException()
    {
        $recorder = new Recorder;
        $result = $recorder->record(new NotifierFailedException);
        $this->assertInstanceOf(\Tylercd100\LERN\Models\ExceptionModel::class, $result);
    }

    public function testGetDataFunction()
    {
        $data = ['user'=>['email','mail@test.com','password'=>'foobar','name'=>'Foo Bar'],'status'=>200];
        Request::replace($data);
        $this->app['config']->set('lern.record.excludeKeys', ['password','email']);
        $recorder = new Recorder;
        $result = $this->invokeMethod($recorder, 'getData', [$data]);
        $this->assertArrayNotHasKey('password', $result['user']);
        $this->assertArrayNotHasKey('email', $result['user']);
    }

    public function testRateLimiting()
    {
        $recorder = new Recorder;
        $result = $recorder->record(new Throwable);
        $this->assertInstanceOf(\Tylercd100\LERN\Models\ExceptionModel::class, $result);

        $result = $recorder->record(new Throwable);
        $this->assertEquals(false, $result);

        sleep(config("lern.ratelimit")+2);

        $result = $recorder->record(new Throwable);
        $this->assertInstanceOf(\Tylercd100\LERN\Models\ExceptionModel::class, $result);
    }
}
