<?php

namespace Tylercd100\LERN\Factories;

use Exception;
use Monolog\Logger;
use Mail;
use Swift_Message;

class MonologHandlerFactory {

    protected $config;

    /**
     * Creates a handler for a specified driver
     * @param  string $driver                    Lowercase driver string that is also in the config/lern.php file
     * @param  string $subject                   Title or Subject line for the notification
     * @return \Monolog\Handler\HandlerInterface A handler to use with a Monolog\Logger instance
     */
    public function create($driver, $subject = null)
    {
        $this->config = config('lern.notify.' . $driver);
        if (is_array($this->config)) {
            return $this->{$driver}($subject);
        } else {
            throw new Exception("config must be an array! You may have chosen an unsupported monolog handler.");
        }
    }

    /**
     * Creates FleepHook Monolog Handler
     * @return \Monolog\Handler\FleepHookHandler A handler to use with a Monolog\Logger instance
     */
    protected function fleephook() {
        return new \Monolog\Handler\FleepHookHandler(
            $this->config['token']
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
            Logger::CRITICAL,
            true, 
            true, 
            'text', 
            'api.hipchat.com', 
            'v2'
        );
    }

    /**
     * Creates Flowdock Monolog Handler
     * @return \Monolog\Handler\FlowdockHandler A handler to use with a Monolog\Logger instance
     */
    protected function flowdock() {
        return new \Monolog\Handler\FlowdockHandler(
            $this->config['token']
        );
    }

    /**
     * Creates Pushover Monolog Handler
     * @param  string $subject  Title or Subject line for the notification
     * @return \Monolog\Handler\PushoverHandler A handler to use with a Monolog\Logger instance
     */
    protected function pushover($subject)
    {
        $this->checkSubject($subject);
        return new \Monolog\Handler\PushoverHandler(
            $this->config['token'],
            $this->config['user'],
            $subject
        );
    }

    /**
     * Creates Mail Monolog Handler
     * @param  string $subject Title or Subject line for the notification
     * @return \Monolog\Handler\NativeMailerHandler|Monolog\Handler\SwiftMailerHandler A handler to use with a Monolog\Logger instance
     */
    protected function mail($subject)
    {
        $this->checkSubject($subject);
        if (isset($this->config['smtp']) && $this->config['smtp']) {
            return new \Monolog\Handler\SwiftMailerHandler(
                Mail::getSwiftMailer(),
                Swift_Message::newInstance($subject)->setFrom($this->config['from'])->setTo($this->config['to'])
            );
        } else {
            return new \Monolog\Handler\NativeMailerHandler(
                $this->config['to'],
                $subject,
                $this->config['from']
            );
        }
    }

    /**
     * Creates Slack Monolog Handler
     * @return \Monolog\Handler\SlackHandler A handler to use with a Monolog\Logger instance
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
     * Creates Plivo Monolog Handler
     * @return \Tylercd100\Monolog\Handler\PlivoHandler A handler to use with a Monolog\Logger instance
     */
    protected function plivo()
    {
        return new \Tylercd100\Monolog\Handler\PlivoHandler(
            $this->config['token'], 
            $this->config['auth_id'], 
            $this->config['from'],
            $this->config['to']
        );
    }

    /**
     * Creates Twilio Monolog Handler
     * @return \Tylercd100\Monolog\Handler\TwilioHandler A handler to use with a Monolog\Logger instance
     */
    protected function twilio()
    {
        return new \Tylercd100\Monolog\Handler\TwilioHandler(
            $this->config['secret'], 
            $this->config['sid'], 
            $this->config['from'],
            $this->config['to']
        );
    }
    /**
     * Validates that the subject is an unempty string
     * @param  mixed $subject The value to check
     * @return void
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