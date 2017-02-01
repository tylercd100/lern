<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\LERN;
use Tylercd100\LERN\Facades\LERN as LERNFacade;
use Tylercd100\LERN\Components\Notifier;
use Tylercd100\LERN\Components\Recorder;
use Tylercd100\Notify\Factories\MonologHandlerFactory;
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

    public function testLERNFacade()
    {
        $obj = LERNFacade::getFacadeRoot();
        $this->assertInstanceOf(LERN::class, $obj);
    }

    public function testItCallsNotifyAndRecord()
    {
        $this->migrate();
        $mock = $this->getMock('Tylercd100\LERN\LERN', array('notify','record'));

        $mock->expects($this->once())
             ->method('notify');

        $mock->expects($this->once())
             ->method('record');

        $mock->handle(new Exception);
        $this->migrateReset();
    }

    public function testRecordReturnsCorrectInstance()
    {
        $this->migrate();
        $lern = new LERN;
        $data = $lern->record(new Exception);
        $this->assertInstanceOf('Tylercd100\LERN\Models\ExceptionModel', $data);
        $this->migrateReset();
    }

    public function testItCallsNotifierSendMethod()
    {
        $mock = $this->getMock('Tylercd100\LERN\Components\Notifier', array('send'));
        $mock->expects($this->once())
             ->method('send');
        $lern = new LERN($mock);
        $lern->notify(new Exception);
    }

    public function testItCallsNotifierPushHandlerMethod()
    {
        $mock = $this->getMock('Tylercd100\LERN\Components\Notifier', array('pushHandler'));
        $mock->expects($this->once())
             ->method('pushHandler');
        $lern = new LERN($mock);
        $handler = (new MonologHandlerFactory)->create('mail', config('lern.notify.mail'), 'Test Subject');
        $lern->pushHandler($handler);
    }

    public function testItCallsNotifierSetSubjectMethod()
    {
        $mock = $this->getMock('Tylercd100\LERN\Components\Notifier', array('setSubject'));

        $mock->expects($this->once())
             ->method('setSubject');

        $lern = new LERN($mock);
        $lern->setSubject("Test Subject");
    }

    public function testItCallsNotifierSetMessageMethod()
    {
        $mock = $this->getMock('Tylercd100\LERN\Components\Notifier', array('setMessage'));

        $mock->expects($this->once())
             ->method('setMessage');

        $lern = new LERN($mock);
        $lern->setMessage("Test Message");
    }

    public function testSettingAndGettingACustomNotifierInstance()
    {
        $lern = new LERN;
        $orig_notifier = new Notifier;
        $lern->setNotifier($orig_notifier);
        $new_notifier = $lern->getNotifier();
        $this->assertEquals($new_notifier, $orig_notifier);
    }

    public function testSettingAndGettingACustomRecorderInstance()
    {
        $lern = new LERN;
        $orig_recorder = new Recorder;
        $lern->setRecorder($orig_recorder);
        $new_recorder = $lern->getRecorder();
        $this->assertEquals($new_recorder, $orig_recorder);
    }
}
