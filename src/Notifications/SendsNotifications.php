<?php

namespace Tylercd100\LERN\Notifications;

interface SendsNotifications
{

    /**
     * @param string $subject
     *
     * @return \Tylercd100\LERN\Notifications\SendsNotifications
     */
    public function setSubject($subject);

    /**
     * @param string $message
     *
     * @return \Tylercd100\LERN\Notifications\SendsNotifications
     */
    public function setMessage($message);

    public function send();
}
