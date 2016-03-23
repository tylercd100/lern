<?php

namespace Tylercd100\LERN\Notifications;

use Exception;
use Monolog\Logger;

class MonologHandlerFactory {

    protected $config;

    /**
     * Creates a handler for a specified driver
     * @param  string $driver                   Lowercase driver string that is also in the config/lern.php file
     * @param  array  $opts                     Extra options
     * @return Monolog\Handler\HandlerInterface A handler to use with a Monolog\Logger instance
     */
    public function create($driver,$opts = array())
    {
        $this->config = config('lern.notify.'.$driver);
        if(is_array($this->config))
            return $this->{$driver}($opts);
    }

    /**
     * Creates Pushover Monolog Handler
     * @param  array $opts     Extra Options
     * @return PushoverHandler A handler to use with a Monolog\Logger instance
     */
    protected function pushover($opts)
    {
        return new \Monolog\Handler\PushoverHandler(
            $this->config['token'],
            $this->config['user'],
            (!empty($opts['subject']) ? $opts['subject'] : $this->config['subject']),
            Logger::ERROR
        );
    }

    /**
     * Creates Mail Monolog Handler
     * @param  array $opts         Extra Options
     * @return NativeMailerHandler A handler to use with a Monolog\Logger instance
     */
    protected function mail($opts)
    {
        return new \Monolog\Handler\NativeMailerHandler(
            $this->config['to'],
            (!empty($opts['subject']) ? $opts['subject'] : $this->config['subject']),
            $this->config['from']
        );
    }

    /**
     * Creates Slack Monolog Handler
     * @param  array $opts   Extra Options
     * @return SlackHandler  A handler to use with a Monolog\Logger instance
     */
    protected function slack($opts)
    {
        return new \Monolog\Handler\SlackHandler(
            $this->config['token'], 
            $this->config['channel'], 
            $this->config['username']
        );
    }
}