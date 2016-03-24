<?php

namespace Tylercd100\LERN;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Tylercd100\LERN\Models\ExceptionModel;
use Tylercd100\LERN\Notifications\Notifier;

/**
* The master class
*/
class LERN 
{

    private $exception;
    private $notifier;
    
    /**
     * @param Notifier|null $notifier Notifier instance
     */
    public function __construct(Notifier $notifier = null)
    {
        if (empty($notifier))
            $notifier = new Notifier();
        $this->notifier = $notifier;
    }

    /**
     * Will execute record and notify methods
     * @param  Exception $e   The exception to use
     * @return ExceptionModel the recorded Eloquent Model
     */
    public function handle(Exception $e)
    {
        $this->exception = $e;
        $this->notify($e);
        return $this->record($e);
    }

    /**
     * Stores the exception in the database
     * @param  Exception $e   The exception to use
     * @return ExceptionModel the recorded Eloquent Model
     */
    public function record(Exception $e)
    {
        $this->exception = $e;
        $opts = [
            'class'       => get_class($e),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'code'        => $e->getCode(),
            'message'     => $e->getMessage(),
            'trace'       => $e->getTraceAsString(),
        ];

        if ($e instanceof HttpExceptionInterface)
            $opts['status_code'] = $e->getStatusCode();

        return ExceptionModel::create($opts);
    }

    /**
     * Will send the exception to all monolog handlers
     * @param  Exception $e The exception to use
     * @return void
     */
    public function notify(Exception $e)
    {
        $this->exception = $e;
        $this->notifier->send($e);
    }

    /**
     * Pushes on another Monolog Handler
     * @param  HandlerInterface $handler The handler instance to add on
     * @return Notifier                  Returns this
     */
    public function pushHandler(HandlerInterface $handler){
        $this->notifier->pushHandler($handler);
        return $this;
    }

    /**
     * Get Notifier
     * @return Notifier 
     */
    public function getNotifier()
    {
        return $this->notifier;
    }

    /**
     * Set Notifier
     * @param Notifier $notifier A Notifier instance to use
     */
    public function setNotifier(Notifier $notifier)
    {
        $this->notifier = $notifier;
        return $this;
    }

    /**
     * Set a string or a closure to be called that will generate the message body for the notification
     * @param function|string $cb This closure function will be passed an Exception and must return a string
     */
    public function setMessage($cb)
    {
        $this->notifier->setMessage($cb);
        return $this;
    }

    /**
     * Set a string or a closure to be called that will generate the subject line for the notification
     * @param function|string $cb This closure function will be passed an Exception and must return a string
     */
    public function setSubject($cb)
    {
        $this->notifier->setSubject($cb);
        return $this;
    }

}