<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\Components\Recorder;
use Exception;

class RecorderTest extends TestCase
{
    public function testCollectMethodReturnsFalseWhenConfigValuesAreFalse(){
        $this->migrate();
        $this->app['config']->set('lern.record.collect', [
            'method'=>false,//When true it will collect GET, POST, DELETE, PUT, etc...
            'data'=>false,//When true it will collect Input data
            'status_code'=>false,
            'user_id'=>false,
            'url'=>false,
        ]);
        $recorder = new Recorder;
        $model = $recorder->record(new Exception);
        $this->assertEquals($model->url,null);
        $this->migrateReset();
    }
}