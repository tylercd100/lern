<?php

namespace Tylercd100\LERN;

use Exception;
use Tylercd100\LERN\LERN;
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
            $lern = new LERN();
            $lern->handle($e);
        }

        //Continue...
        return parent::report($e);
    }
}