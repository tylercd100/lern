<?php

namespace Tylercd100\LERN;

use Exception;
use Tylercd100\LERN\Model\ExceptionModel as Error;
use Tylercd100\LERN\Notifications\Notifier;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
    
class Handler extends ExceptionHandler {

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($this->shouldReport($e)) {
            $this->sendExceptionNotifications($e);
            $this->recordException($e);
        }

        //Continue...
        return parent::report($e);
    }

    /**
     * Send exception notifications to log, mail, etc...
     * @param  Exception $e [description]
     * @return [type]       [description]
     */
    protected function sendExceptionNotifications(Exception $e){
        $notifier = new Notifier();
        $notifier->sendException($e);
    }

    /**
     * Stores the exception in the database
     * @param  Exception $e [description]
     * @return [type]       [description]
     */
    protected function recordException(Exception $e){
        $opts = [
            'class'       => get_class($e),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'code'        => $e->getCode(),
            'message'     => $e->getMessage(),
            'trace'       => $e->getTraceAsString(),
        ];

        if($e instanceof HttpExceptionInterface)
            $opts['status_code'] = $e->getStatusCode();

        return Error::create($opts);
    }
}