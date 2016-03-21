<?php

namespace Tylercd100\LERN\Notifications\Drivers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;
use Tylercd100\LERN\Notifications\BaseDriver;

class Mail extends BaseDriver
{
    /** @var Mailer */
    protected $mailer;

    /** @var array */
    protected $config;

    /**
     * @param Mailer     $mailer
     * @param Repository $config
     */
    public function __construct(Mailer $mailer, Repository $config)
    {
        $this->config = $config->get('lern.notify.mail');

        $this->mailer = $mailer;
    }

    public function send()
    {
        $this->mailer->raw($this->message, function (Message $message) {

            $message
                ->subject($this->subject)
                ->from($this->config['from'])
                ->to($this->config['to']);
        });
    }
}
