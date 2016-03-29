<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\Components\Recorder;
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
        
        $this->setExpectedException('Tylercd100\LERN\Exceptions\RecorderFailedException');

        $mock->record(new Exception);
    }
}