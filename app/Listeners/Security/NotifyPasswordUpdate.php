<?php

namespace App\Listeners\Security;

use App\Events\Security\PasswordSetEvent;
use App\Notification\Security\PasswordSetEmail;

class NotifyPasswordUpdate
{
    /**
     * Handle the event
     *
     * @param PasswordSetEvent $event
     * @return void
     */
    public function handle(PasswordSetEvent $event)
    {
        $event->user->notify(new PasswordSetEmail($event->user, $event->initialization));
    }
}