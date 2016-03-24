<?php

namespace Tylercd100\LERN\Notifications;

use Exception;
use Monolog\Logger;
use Monolog\Handler\HandlerInterface;
use Tylercd100\LERN\Notifications\MonologHandlerFactory;

class Notifier {
    protected $config;
    protected $log;
    protected $messageCb;
    protected $subjectCb;

    /**
     * You can provide a Monolog Logger instance to use in the constructor 
     * @param Logger|null $log Logger instance to use
     */
    public function __construct(Logger $log = null) {
        if ($log === null) {
            $log = new Logger(config('lern.notify.channel'));
        }

        $this->log = $log;
        $this->config = config('lern.notify');
    }

    /**
     * Set a string or a closure to be called that will generate the message body for the notification
     * @param function|string $cb This closure function will be passed an Exception and must return a string
     */
    public function setMessage($cb)
    {
        if (is_string($cb)) {
            $this->messageCb = function() use ($cb) { return $cb; };
        } else if (is_callable($cb)) {
            $this->messageCb = $cb;
        }

        return $this;
    }

    /**
     * Returns the result of the message closure
     * @param  Exception $e The Exception instance that you want to build the message around
     * @return string       The message string
     */
    public function getMessage(Exception $e) {
        if (is_callable($this->messageCb)) {
            return $this->messageCb->__invoke($e);
        } else {
            $msg = get_class($e) . " was thrown! \n" . $e->getMessage();
            if ($this->config['includeExceptionStackTrace'] === true) {
                $msg .= "\n\n" . $e->getTraceAsString();
            }
            return $msg;
        }
    }

    /**
     * Set a string or a closure to be called that will generate the subject line for the notification
     * @param function|string $cb This closure function will be passed an Exception and must return a string
     */
    public function setSubject($cb)
    {
        if (is_string($cb)) {
            $this->subjectCb = function() use ($cb) { return $cb; };
        } else if (is_callable($cb)) {
            $this->subjectCb = $cb;
        }

        return $this;
    }

    /**
     * Returns the result of the subject closure
     * @param  Exception $e The Exception instance that you want to build the subject around
     * @return string       The subject string
     */
    public function getSubject(Exception $e) {
        if (is_callable($this->subjectCb)) {
            return $this->subjectCb->__invoke($e);
        } else {
            return get_class($e);
        }
    }

    /**
     * Pushes on another Monolog Handler
     * @param  HandlerInterface $handler The handler instance to add on
     * @return Notifier                  Returns this
     */
    public function pushHandler(HandlerInterface $handler) {
        $this->log->pushHandler($handler);
        return $this;
    }

    /**
     * Triggers the Monolog Logger instance to log an error to all handlers
     * @param  Exception $e The exception to use
     * @return Notifier     Returns this
     */
    public function send(Exception $e) {
        $factory = new MonologHandlerFactory();
        $drivers = $this->config['drivers'];

        $message = $this->getMessage($e);
        $subject = $this->getSubject($e);
        
        foreach ($drivers as $driver) {
            $handler = $factory->create($driver, $subject);
            $this->log->pushHandler($handler);
        }

        $this->log->addError($message);

        return $this;
    }
}