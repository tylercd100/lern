<?php

namespace Tylercd100\LERN\Components;

use Exception;

abstract class Component {

    /**
     * @var array
     */
    protected $dontHandle = [];

    /**
     * @var array
     */
    private $absolutelyDontHandle = [
        \Tylercd100\LERN\Exceptions\RecorderFailedException::class,
        \Tylercd100\LERN\Exceptions\NotifierFailedException::class,
    ];

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