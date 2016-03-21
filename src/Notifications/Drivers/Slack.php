<?php

namespace Tylercd100\LERN\Notifications\Drivers;

use Illuminate\Contracts\Config\Repository;
use Maknz\Slack\Client;
use Tylercd100\LERN\Notifications\BaseDriver;

class Slack extends BaseDriver
{
    /** @var \Maknz\Slack\Client */
    protected $client;

    /** @var array */
    protected $config;

    /**
     * @param \Maknz\Slack\Client $client
     * @param Repository          $config
     */
    public function __construct(Client $client, Repository $config)
    {
        $this->config = $config->get('lern.notify.slack');

        $client->setDefaultUsername($this->config['username']);
        $client->setDefaultIcon($this->config['icon']);

        $this->client = $client;
    }

    public function send()
    {
        $this->client
            ->to($this->config['channel'])
            ->attach([
                'text' => $this->message,
                'color' => 'warning',
            ])
            ->send($this->subject);
    }
}
