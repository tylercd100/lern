<?php

namespace Tylercd100\LERN\Notifications;

use Exception;
use Monolog\Logger;

class MonologHandlerFactory {

    protected $config;

    /**
     * Creates a handler for a specified driver
     * @param  string $driver                   Lowercase driver string that is also in the config/lern.php file
     * @param  array  $subject                  Title or Subject line for the notification
     * @return Monolog\Handler\HandlerInterface A handler to use with a Monolog\Logger instance
     */
    public function create($driver,$subject = null)
    {
        $this->config = config('lern.notify.'.$driver);
        if(is_array($this->config))
            return $this->{$driver}($subject);
    }

    /**
     * Creates Pushover Monolog Handler
     * @param  array $subject  Title or Subject line for the notification
     * @return PushoverHandler A handler to use with a Monolog\Logger instance
     */
    protected function pushover($subject)
    {
        $this->checkSubject($subject);
        return new \Monolog\Handler\PushoverHandler(
            $this->config['token'],
            $this->config['user'],
            $subject,
            Logger::ERROR
        );
    }

    /**
     * Creates Mail Monolog Handler
     * @param  array $subject      Title or Subject line for the notification
     * @return NativeMailerHandler A handler to use with a Monolog\Logger instance
     */
    protected function mail($subject)
    {
        $this->checkSubject($subject);
        return new \Monolog\Handler\NativeMailerHandler(
            $this->config['to'],
            $subject,
            $this->config['from']
        );
    }

    /**
     * Creates Slack Monolog Handler
     * @param  array $subject Title or Subject line for the notification
     * @return SlackHandler   A handler to use with a Monolog\Logger instance
     */
    protected function slack()
    {
        return new \Monolog\Handler\SlackHandler(
            $this->config['token'], 
            $this->config['channel'], 
            $this->config['username']
        );
    }

    /**
     * Validates that the subject is an unempty string
     * @param  mixed $subject [description]
     * @return [type]          [description]
     */
    private function checkSubject($subject){
        if(empty($subject)) {
            throw new Exception('$subject must not be empty!');
        }

        if(!is_string($subject)) {
            throw new Exception('$subject must be a string!');
        }
    }
}