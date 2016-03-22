<?php

namespace Tylercd100\LERN\Notifications;

use Exception;
use Monolog\Logger;
use Tylercd100\LERN\Notifications\MonologHandlerFactory;

class Notifier {
    protected $config;
    protected $log;

    public function __construct(Logger $log = null){
        if($log === null){
            $log = new Logger('Tylercd100\LERN');
        }

        $this->log = $log;
        $this->config = config('lern.notify');
    }

    public function pushHandler($handler){
        $this->log->pushHandler($handler);
        return $this;
    }

    public function send(Exception $e){
        $factory = new MonologHandlerFactory();
        $drivers = $this->config['drivers'];

        foreach ($drivers as $driver) {
            $handler = $factory->create($driver,['subject'=>get_class($e)]);
            $this->log->pushHandler($handler);
        }

        //TODO: Figure out how to change this message!
        $this->log->addError(get_class($e) . " was thrown! \n".$e->getMessage());

        return;
    }
}