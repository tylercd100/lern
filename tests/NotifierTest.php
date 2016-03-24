<?php

namespace Tylercd100\LERN\Tests;

use Exception;
use Tylercd100\LERN\Notifications\Notifier;
use Tylercd100\LERN\Notifications\MonologHandlerFactory;

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
    }

    public function testLoggerCallsAddsError()
    {
        $this->app['config']->set('lern.notify.drivers', ['slack','pushover']);

        $observer = $this->getMock('Monolog\Logger',['addError'],['channelName']);
        $observer->expects($this->once())
                 ->method('addError');

        $subject = new Notifier($observer);
        $subject->send(new Exception);
    }

    public function testLoggerCallsPushesHandler()
    {
        $handler = (new MonologHandlerFactory())->create('slack');

        $observer = $this->getMock('Monolog\Logger',['pushHandler'],['channelName']);
        $observer->expects($this->once())
                 ->method('pushHandler');

        $subject = new Notifier($observer);
        $subject->pushHandler($handler);
    }

    public function testNotifierReturnsTheCorrectMessageWhenUsingClosure(){
        $this->notifier->setMessage(function($e){return "This is a test";});
        $result = $this->notifier->getMessage(new Exception);
        $this->assertEquals($result,"This is a test");
    }

    public function testNotifierReturnsTheCorrectMessageWhenUsingString(){
        $this->notifier->setMessage("This is a test");
        $result = $this->notifier->getMessage(new Exception);
        $this->assertEquals($result,"This is a test");
    }

    public function testNotifierReturnsTheCorrectSubjectWhenUsingClosure(){
        $this->notifier->setSubject(function($e){return "This is a test";});
        $result = $this->notifier->getSubject(new Exception);
        $this->assertEquals($result,"This is a test");
    }

    public function itReturnsTheCorrectSubjectWhenUsingString(){
        $this->notifier->setSubject("This is a test");
        $result = $this->notifier->getSubject(new Exception);
        $this->assertEquals($result,"This is a test");
    }

}