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
     * @return \Monolog\Handler\HandlerInterface A handler to use with a Monolog\Logger instance
     */
    public function create($driver, $subject = null)
    {
        $this->config = config('lern.notify.' . $driver);
        if (is_array($this->config)) {
                    return $this->{$driver}($subject);
        }
    }

    /**
     * Creates FleepHook Monolog Handler
     * @return \Monolog\Handler\FleepHookHandler A handler to use with a Monolog\Logger instance
     */
    protected function fleephook() {
        return new \Monolog\Handler\FleepHookHandler(
            $this->config['token'],
            Logger::ERROR
        );
    }

    /**
     * Creates HipChat Monolog Handler
     * @return \Monolog\Handler\HipChatHandler A handler to use with a Monolog\Logger instance
     */
    protected function hipchat() {
        return new \Monolog\Handler\HipChatHandler(
            $this->config['token'],
            $this->config['room'],
            $this->config['name'],
            $this->config['notify'],
            Logger::ERROR
        );
    }

    /**
     * Creates Flowdock Monolog Handler
     * @return \Monolog\Handler\FlowdockHandler A handler to use with a Monolog\Logger instance
     */
    protected function flowdock() {
        return new \Monolog\Handler\FlowdockHandler(
            $this->config['token'],
            Logger::ERROR
        );
    }

    /**
     * Creates Pushover Monolog Handler
     * @param  array $subject  Title or Subject line for the notification
     * @return \Monolog\Handler\PushoverHandler A handler to use with a Monolog\Logger instance
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
     * @return \Monolog\Handler\NativeMailerHandler A handler to use with a Monolog\Logger instance
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
     * @return \Monolog\Handler\SlackHandler   A handler to use with a Monolog\Logger instance
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
    private function checkSubject($subject) {
        if (empty($subject)) {
            throw new Exception('$subject must not be empty!');
        }

        if (!is_string($subject)) {
            throw new Exception('$subject must be a string!');
        }
    }
}