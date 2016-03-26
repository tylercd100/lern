<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\LERN;
use Exception;

class LERNTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();        
    }

    public function testItCallsNotifyAndRecord(){
        $this->migrate();
        $mock = $this->getMock('Tylercd100\LERN\LERN', array('notify','record'));
        
        $mock->expects($this->once())
             ->method('notify');

        $mock->expects($this->once())
             ->method('record');

        $mock->handle(new Exception);
        $this->migrateReset();
    }

    public function testRecordReturnsCorrectInstance(){
        $this->migrate();
        $lern = new LERN;
        $data = $lern->record(new Exception);
        $this->assertInstanceOf('Tylercd100\LERN\Models\ExceptionModel',$data);
        $this->migrateReset();
    }

    public function testItCallsNotifierSendMethod(){
        $mock = $this->getMock('Tylercd100\LERN\Notifications\Notifier', array('send'));
        $mock->expects($this->once())
             ->method('send');
        $lern = new LERN($mock);
        $lern->notify(new Exception);
    }
}