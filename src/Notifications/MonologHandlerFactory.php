<?php

namespace Tylercd100\LERN\Notifications;

use Exception;
use Monolog\Logger;

class MonologHandlerFactory {

    protected $config;

    public function __construct()
    {
        $this->config = config('lern.notify');
    }

    public function create($driver,$opts = array())
    {
        if(isset($this->config[$driver]) && is_array($this->config[$driver]))
            return $this->{$driver}($this->config[$driver],$opts);
    }

    protected function pushover($config, $opts)
    {
        return new \Monolog\Handler\PushoverHandler(
            $config['token'],
            $config['user'],
            (!empty($opts['subject']) ? $opts['subject'] : $config['subject']),
            Logger::ERROR
        );
    }

    protected function mail($config, $opts)
    {
        return new \Monolog\Handler\NativeMailerHandler(
            $config['to'],
            (!empty($opts['subject']) ? $opts['subject'] : $config['subject']),
            $config['from']
        );
    }

    protected function slack($config, $opts)
    {
        return new \Monolog\Handler\SlackHandler(
            $config['token'], 
            $config['channel'], 
            $config['username'], 
            $config['useAttachment'], 
            $config['iconEmoji']
        );
    }
}