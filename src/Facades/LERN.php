<?php

namespace Tylercd100\LERN\Facades;

use Illuminate\Support\Facades\Facade;

class LERN extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lern';
    }
}