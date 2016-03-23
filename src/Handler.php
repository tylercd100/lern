<?php

namespace Tylercd100\LERN;

use Exception;
use Tylercd100\LERN\Facades\LERN;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
    
class Handler extends ExceptionHandler {

    /**
     * Handle the uncaught Exception
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($this->shouldReport($e)) {
            LERN::handle($e);
        }

        //Continue...
        return parent::report($e);
    }
}