<?php

namespace Tylercd100\LERN\Notifications;

abstract class BaseDriver implements SendsNotifications
{

    /** @var string */
    protected $subject;

    /** @var string */
    protected $message;

    /**
     * @param string $subject
     *
     * @return \Tylercd100\LERN\Notifications\SendsNotifications
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return \Tylercd100\LERN\Notifications\SendsNotifications
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
