<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\Components\Recorder;
use Tylercd100\LERN\Exceptions\RecorderFailedException;
use Exception;

class RecorderTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->migrate();
    }

    public function tearDown()
    {
        $this->migrateReset();
        parent::tearDown();        
    }

    public function testExcludeKeysRemovesTheCorrectValues(){
        $data = ['email'=>'mail@test.com','password'=>'foobar','name'=>'Foo Bar'];
        $this->app['config']->set('lern.record.excludeKeys', ['password','email']);
        $recorder = new Recorder;
        $result = $this->invokeMethod($recorder,'excludeKeys',[$data]);
        $this->assertArrayNotHasKey('password',$result);
        $this->assertArrayNotHasKey('email',$result);
    }

    public function testExcludeKeysRemovesNestedValues(){
        $data = ['user'=>['email','mail@test.com','password'=>'foobar','name'=>'Foo Bar'],'status'=>200];
        $this->app['config']->set('lern.record.excludeKeys', ['password','email']);
        $recorder = new Recorder;
        $result = $this->invokeMethod($recorder,'excludeKeys',[$data]);
        $this->assertArrayNotHasKey('password',$result['user']);
        $this->assertArrayNotHasKey('email',$result['user']);
    }

    public function testCollectMethodReturnsFalseWhenConfigValuesAreFalse(){
        $this->app['config']->set('lern.record.collect', ['method'=>false,'data'=>false,'status_code'=>false,'user_id'=>false,'url'=>false,]);
        $recorder = new Recorder;
        $model = $recorder->record(new Exception);
        $this->assertEquals($model->url,null);
    }

    public function testItThrowsRecorderFailedExceptionWhenExceptionIsThrown(){
        $mock = $this->getMock(Recorder::class,['collect']);

        $mock->expects($this->once())
                 ->method('collect')
                 ->will($this->throwException(new Exception));
        
        $this->setExpectedException(RecorderFailedException::class);

        $mock->record(new Exception);
    }

    public function testRecordShouldReturnFalseWhenPassedRecorderFailedException(){
        $recorder = new Recorder;
        $result = $recorder->record(new RecorderFailedException);
        $this->assertEquals(false,$result);
    }

}