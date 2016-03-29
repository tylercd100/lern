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
        Tylercd100\LERN\Exceptions\LERNExceptionInterface::class,
    ];

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function shouldHandle(Exception $e){
        return !$this->shouldHandle($e);
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function shouldntHandle(Exception $e){
        $dontHandle = array_merge($this->dontHandle,$this->absolutelyDontHandle);

        foreach ($dontHandle as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }

        return false;
    }
}