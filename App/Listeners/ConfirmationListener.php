<?php


namespace App\Listeners;


use App\Callback\Event\ConfirmationEvent;

class ConfirmationListener extends Listener
{
    /**
     * @param ConfirmationEvent $event
     */
    public function handle($event): void
    {
        if ($event->groupId() != $this->config->groupId()) {
            print 'no';
        } else {
            print $this->config->confirmationToken();
        }
    }
}