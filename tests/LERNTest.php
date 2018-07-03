<?php

namespace Tylercd100\LERN\Tests;

use Tylercd100\LERN\LERN;
use Tylercd100\LERN\Facades\LERN as LERNFacade;
use Tylercd100\LERN\Components\Notifier;
use Tylercd100\LERN\Components\Recorder;
use Tylercd100\LERN\Exceptions\RecorderFailedException;
use Tylercd100\Notify\Factories\MonologHandlerFactory;
use Exception;

class LERNTest extends TestCase
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

    public function testLERNFacade()
    {
        $obj = LERNFacade::getFacadeRoot();
        $this->assertInstanceOf(LERN::class, $obj);
    }

    public function testItCallsNotifyAndRecord()
    {
        $mock = $this->getMockBuilder(LERN::class)
            ->setMethods(['notify','record'])
            ->getMock();

        $mock->expects($this->once())
             ->method('notify');

        $mock->expects($this->once())
             ->method('record');

        $mock->handle(new Exception);
    }

    public function testRecordReturnsCorrectInstance()
    {
        $lern = new LERN;
        $data = $lern->record(new Exception);
        $this->assertInstanceOf('Tylercd100\LERN\Models\ExceptionModel', $data);
    }

    public function testItCallsNotifierSendMethod()
    {
        $mock = $this->getMockBuilder('Tylercd100\LERN\Components\Notifier')->setMethods(array('send'))->getMock();
        $mock->expects($this->once())
             ->method('send');
        $lern = new LERN($mock);
        $lern->notify(new Exception);
    }

    public function testItCallsNotifierPushHandlerMethod()
    {
        $mock = $this->getMockBuilder('Tylercd100\LERN\Components\Notifier')->setMethods(array('pushHandler'))->getMock();
        
        $mock->expects($this->once())
             ->method('pushHandler');
             
        $lern = new LERN($mock);
        $handler = (new MonologHandlerFactory)->create('mail', config('lern.notify.mail'), 'Test Subject');
        $lern->pushHandler($handler);
    }

    public function testItCallsNotifierSetSubjectMethod()
    {
        $mock = $this->getMockBuilder('Tylercd100\LERN\Components\Notifier')->setMethods(array('setSubject'))->getMock();

        $mock->expects($this->once())
             ->method('setSubject');

        $lern = new LERN($mock);
        $lern->setSubject("Test Subject");
    }

    public function testItCallsNotifierSetMessageMethod()
    {
        $mock = $this->getMockBuilder('Tylercd100\LERN\Components\Notifier')->setMethods(array('setMessage'))->getMock();

        $mock->expects($this->once())
             ->method('setMessage');

        $lern = new LERN($mock);
        $lern->setMessage("Test Message");
    }

    public function testItCallsNotifierSetLogLevelMethod()
    {
        $mock = $this->getMockBuilder('Tylercd100\LERN\Components\Notifier')->setMethods(array('setLogLevel'))->getMock();

        $mock->expects($this->once())
             ->method('setLogLevel');

        $lern = new LERN($mock);
        $lern->setLogLevel("debug");
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

    public function testSettingAndGettingLogLevels()
    {
        $lern = new LERN;
        $level = "debug";
        $lern->setLogLevel($level);
        $result = $lern->getLogLevel();
        $this->assertEquals($result, $level);
    }

    public function testCantConnectToDatabaseError()
    {
        $lern = new LERN;

        // Mysql should not work as we have not configured it properly.
        // this should reproduce an error similar to having the database offline.
        \Config::set("database.default", "mysql"); 
        $this->expectException(RecorderFailedException::class);
        $lern->handle(new Exception);

        
    }
}
