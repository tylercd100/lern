<?php

namespace Tylercd100\LERN\Components;

use Exception;

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

        return false;
    }
}