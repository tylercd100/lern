<?php

namespace Tylercd100\LERN\Notifications;

use Exception;

class Notifier {
    private $config;

    public function __construct(){
        $this->config = config('lern.notify');
    }

    public function sendException(Exception $e){
        
        $drivers = $this->config['drivers'];

        if(!is_array($drivers))
            return false;

        $subject = get_class($e) . " was thrown!";
        $message = "{$e->getFile()}:{$e->getLine()} {$e->getMessage()}";

        foreach ($drivers as $driver) {
            $stackTrace = "";
            if($this->config[$driver]['includeExceptionStackTrace'] === true){
                $stackTrace = PHP_EOL.$e->getTraceAsString();
            }

            $sender = $this->createDriverInstance($driver);
            $sender->setSubject($subject)
                ->setMessage($message.$stackTrace)
                ->send();
        }

        return true;
    }

    private function createDriverInstance($driver){
        $file = __DIR__.'/Drivers/'.ucfirst($driver).'.php';

        if (file_exists($file)) {
            return app('\\Tylercd100\\LERN\\Notifications\\Drivers\\'.ucfirst($driver));
        } else {
            throw new Exception("Driver '{$driver}' doesn't exist");
        }
    }
}