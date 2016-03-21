<?php

namespace Tylercd100\LERN\Notifications\Drivers;

use Illuminate\Contracts\Logging\Log as LogContract;
use Tylercd100\LERN\Notifications\BaseDriver;

class Log extends BaseDriver
{
    /** @var \Illuminate\Contracts\Logging\Log */
    protected $log;

    /**
     * @param \Illuminate\Contracts\Logging\Log $log
     */
    public function __construct(LogContract $log)
    {
        $this->log = $log;
    }

    public function send()
    {
        $method = ($this->type === static::TYPE_SUCCESS ? 'info' : 'error');

        $this->log->$method("{$this->subject}: {$this->message}");
    }
}
