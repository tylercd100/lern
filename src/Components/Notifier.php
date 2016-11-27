<?php

namespace Tylercd100\LERN\Components;

use Exception;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Tylercd100\LERN\Exceptions\NotifierFailedException;
use Tylercd100\Notify\Drivers\FromConfig as Notify;
use View;

class Notifier extends Component
{
    protected $config;
    protected $log;
    protected $messageCb;
    protected $subjectCb;
    protected $contextCb;

    /**
     * You can provide a Monolog Logger instance to use in the constructor
     * @param Logger|null $log Logger instance to use
     */
    public function __construct(Logger $log = null)
    {
        if ($log === null) {
            $log = new Logger(config('lern.notify.channel'));
        }

        $this->config = config('lern.notify');
        $this->log = $log;
    }

    /**
     * Transforms a value into a closure that returns itself when called
     * @param  callable|string $cb The value that you want to wrap in a closure
     * @return callable
     */
    private function wrapValueInClosure($cb)
    {
        if (is_callable($cb)) {
            return $cb;
        } else {
            return function () use ($cb) { return $cb; };
        }
    }

    /**
     * Set a string or a closure to be called that will generate the message body for the notification
     * @param callable|string $cb A closure or string that will be set for the message
     * @return $this
     */
    public function setMessage($cb)
    {
        $this->messageCb = $this->wrapValueInClosure($cb);
        return $this;
    }

    /**
     * Returns the result of the message closure
     * @param  Exception $e The Exception instance that you want to build the message around
     * @return string       The message string
     */
    public function getMessage(Exception $e)
    {
        $view = $this->config["view"];
        if (!empty($view) && View::exists($view)) {
            return View::make($this->config["view"], ["exception" => $e])->render();
        } elseif (is_callable($this->messageCb)) {
            return $this->messageCb->__invoke($e);
        } else {
            $msg = get_class($e)." was thrown! \n".$e->getMessage();
            if ($this->config['includeExceptionStackTrace'] === true) {
                $msg .= "\n\n".$e->getTraceAsString();
            }
            return $msg;
        }
    }

    /**
     * Set a string or a closure to be called that will generate the subject line for the notification
     * @param callable|string $cb A closure or string that will be set for the subject line
     * @return $this
     */
    public function setSubject($cb)
    {
        $this->subjectCb = $this->wrapValueInClosure($cb);
        return $this;
    }

    /**
     * Returns the result of the subject closure
     * @param  Exception $e The Exception instance that you want to build the subject around
     * @return string       The subject string
     */
    public function getSubject(Exception $e)
    {
        if (is_callable($this->subjectCb)) {
            return $this->subjectCb->__invoke($e);
        } else {
            return get_class($e);
        }
    }

    /**
     * Set an array or a closure to be called that will generate the context array for the notification
     * @param callable|array $cb A closure or array that will be set for the context
     * @return $this
     */
    public function setContext($cb)
    {
        $this->contextCb = $this->wrapValueInClosure($cb);
        return $this;
    }

    /**
     * Returns the result of the context closure
     * @param  Exception $e The Exception instance that you want to build the context around
     * @return array        The context array
     */
    public function getContext(Exception $e, $context = [])
    {
        //This needs a better solution. How do I set specific context needs for different drivers?
        if (in_array('pushover', $this->config['drivers'])) {
            $context['sound'] = $this->config['pushover']['sound'];
        }

        // Call the callback or return the default
        if (is_callable($this->contextCb)) {
            return $this->contextCb->__invoke($e, $context);
        } else {
            return $context;
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
     * @param  array $context Additional information that you would like to pass to Monolog
     * @return bool
     * @throws NotifierFailedException
     */
    public function send(Exception $e, array $context = [])
    {
        if ($this->shouldntHandle($e)) {
            return false;
        }

        $message = $this->getMessage($e);
        $subject = $this->getSubject($e);
        $context = $this->getContext($e, $context);

        try {
            $notify = new Notify($this->config, $this->log, $subject);

            $level = (array_key_exists('log_level', $this->config) && !empty($this->config['log_level']))
                ? $this->config['log_level']
                : 'critical';

            $notify->{$level}($message, $context);

            return true;
        } catch (Exception $e) {
            $code = (is_int($e->getCode()) ? $e->getCode() : 0);
            throw new NotifierFailedException($e->getMessage(), $code, $e);
        }
    }
}
