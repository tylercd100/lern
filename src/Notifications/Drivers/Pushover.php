<?php

namespace Tylercd100\LERN\Notifications\Drivers;

use Illuminate\Contracts\Config\Repository;
use Tylercd100\LERN\Notifications\BaseDriver;

class Pushover extends BaseDriver
{
    /** @var array */
    protected $config;

    /**
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config->get('lern.notify.pushover');
    }

    public function send()
    {
        curl_setopt_array($ch = curl_init(), [

            CURLOPT_URL => 'https://api.pushover.net/1/messages.json',
            CURLOPT_POSTFIELDS => [
                'token' => $this->config['token'],
                'user' => $this->config['user'],
                'title' => $this->subject,
                'message' => $this->message,
                'sound' => !empty($this->config['sound']) ? $this->config['sound'] : 'siren',
            ],
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
}
