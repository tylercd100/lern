<?php

namespace Tylercd100\LERN\Components;

use Exception;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

abstract class Component {

    /**
     * @var array
     */
    protected $dontHandle = [];

    /**
     * This array is overwritten in each component
     * 
     * @var array
     */
    protected $absolutelyDontHandle = [];

    /**
     * Determine if the exception is in the "do not handle" list.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function shouldntHandle(Exception $e) {
        $dontHandle = array_merge($this->dontHandle, $this->absolutelyDontHandle);

        foreach ($dontHandle as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }

        $sent_at = Cache::get($this->getCacheKey($e));
        if (empty($sent_at) || $sent_at->addSeconds(config('lern.ratelimit', 1))->lte(Carbon::now())) {
            return false; // The cache is empty or enough time has passed, so lets continue
        } else {
            return true;
        }
    }

    /**
     * Returns the cache key for the exception with the current component
     * 
     * @param \Exception $e
     * @return string
     */
    protected function getCacheKey(Exception $e)
    {
        return "LERN::".static::class."::".get_class($e);
    }
}