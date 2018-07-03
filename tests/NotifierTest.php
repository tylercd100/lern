<?php

namespace Tylercd100\LERN\Tests;

use Exception;
use Tylercd100\LERN\Components\Notifier;
use Tylercd100\LERN\Exceptions\NotifierFailedException;
use Tylercd100\LERN\Exceptions\RecorderFailedException;
use Tylercd100\Notify\Factories\MonologHandlerFactory;

class NotifierTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->notifier = new Notifier;
    }

    public function tearDown()
    {
        unset($this->notifier);
        parent::tearDown();
    }

    public function testAllSupportedDrivers()
    {
        $this->app['config']->set('lern.notify.drivers', $this->supportedDrivers);

        $observer = $this->getMockBuilder('Monolog\Logger')
            ->setMethods(['critical'])
            ->setConstructorArgs(['channelName'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('critical');

        $subject = new Notifier($observer);
        $subject->send(new Exception);
    }

    public function testSendsDifferentLogLevels()
    {
        $logLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

        $this->app['config']->set('lern.notify.drivers', ['slack']);

        foreach ($logLevels as $logLevel) {
            $this->app['config']->set('lern.notify.log_level', $logLevel);

            $observer = $this->getMockBuilder('Monolog\Logger')
                ->setMethods([$logLevel])
                ->setConstructorArgs(['channelName'])
                ->getMock();
            $observer->expects($this->once())
                     ->method($logLevel);

            $subject = new Notifier($observer);
            $subject->send(new Exception);
        }
    }

    public function testLoggerCallsAddsError()
    {
        $this->app['config']->set('lern.notify.drivers', ['slack','pushover']);

        $observer = $this->getMockBuilder('Monolog\Logger')
            ->setMethods(['critical'])
            ->setConstructorArgs(['channelName'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('critical');

        $subject = new Notifier($observer);
        $subject->send(new Exception);
    }

    public function testLoggerCallsPushesHandler()
    {
        $handler = (new MonologHandlerFactory())->create('slack', config('lern.notify.slack'));

        $observer = $this->getMockBuilder('Monolog\Logger')
            ->setMethods(['pushHandler'])
            ->setConstructorArgs(['channelName'])
            ->getMock();
        $observer->expects($this->once())
                 ->method('pushHandler');

        $subject = new Notifier($observer);
        $subject->pushHandler($handler);
    }


    public function testNotifierReturnsTheCorrectMessageWhenUsingNoCallbackAndNoView()
    {
        config(['lern.notify.view' => null]);
        $this->notifier = new Notifier;
        $result = $this->notifier->getMessage(new Exception);
        $this->assertContains('Exception was thrown!', $result);
    }

    public function testNotifierReturnsTheCorrectMessageWhenUsingTheDefaultView()
    {
        config(['lern.notify.view' => "exceptions.default"]);
        $this->notifier = new Notifier;
        $result = $this->notifier->getMessage(new Exception);
        $this->assertNotEmpty($result);
    }

    public function testNotifierReturnsTheCorrectMessageWhenUsingClosure()
    {
        config(['lern.notify.view' => null]);
        $this->notifier = new Notifier;
        $this->notifier->setMessage(function ($e) {
            return "This is a test";
        });
        $result = $this->notifier->getMessage(new Exception);
        $this->assertEquals($result, "This is a test");
    }

    public function testNotifierReturnsTheCorrectMessageWhenUsingView()
    {
        $result = $this->notifier->getMessage(new Exception);
        $this->assertEquals($result, "<h1>Hello</h1>");
    }

    public function testNotifierReturnsTheCorrectContextWhenUsingClosure()
    {
        $this->notifier->setContext(function ($e, $context) {
            return ["text"=>"This is a test"];
        });
        $result = $this->notifier->getContext(new Exception);
        $this->assertEquals($result, ["text"=>"This is a test"]);
    }

    public function testNotifierReturnsTheCorrectMessageWhenUsingString()
    {
        config(['lern.notify.view' => null]);
        $this->notifier = new Notifier;
        $this->notifier->setMessage("This is a test");
        $result = $this->notifier->getMessage(new Exception);
        $this->assertEquals($result, "This is a test");
    }

    public function testNotifierReturnsTheCorrectSubjectWhenUsingClosure()
    {
        $this->notifier->setSubject(function ($e) {
            return "This is a test";
        });
        $result = $this->notifier->getSubject(new Exception);
        $this->assertEquals($result, "This is a test");
    }

    public function testItReturnsTheCorrectSubjectWhenUsingString()
    {
        $this->notifier->setSubject("This is a test");
        $result = $this->notifier->getSubject(new Exception);
        $this->assertEquals($result, "This is a test");
    }

    public function testItThrowsNotifierFailedExceptionWhenMonologThrowsException()
    {

        $handler = (new MonologHandlerFactory())->create('slack', config('lern.notify.slack'));

        $observer = $this->getMockBuilder('Monolog\Logger')
            ->setMethods(['critical'])
            ->setConstructorArgs(['channelName'])
            ->getMock();

        $observer->expects($this->once())
                 ->method('critical')
                 ->will($this->throwException(new Exception));

        $this->expectException('Tylercd100\LERN\Exceptions\NotifierFailedException');

        $subject = new Notifier($observer);
        $subject->pushHandler($handler);
        $subject->send(new Exception);
    }

    public function testSendShouldReturnFalseWhenPassedNotifierFailedException()
    {
        $notifier = new Notifier;
        $result = $notifier->send(new NotifierFailedException);
        $this->assertEquals(false, $result);
    }

    public function testSendShouldReturnTrueWhenPassedRecorderFailedException()
    {
        $notifier = new Notifier;
        $result = $notifier->send(new RecorderFailedException);
        $this->assertEquals(true, $result);
    }

    public function testRateLimiting()
    {
        $notifier = new Notifier;
        $result = $notifier->send(new Exception);
        $this->assertEquals(true, $result);

        $result = $notifier->send(new Exception);
        $this->assertEquals(false, $result);

        sleep(2);

        $result = $notifier->send(new Exception);
        $this->assertEquals(true, $result);
    }
}
