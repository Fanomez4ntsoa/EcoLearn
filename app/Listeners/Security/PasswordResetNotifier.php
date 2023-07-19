<?php

namespace App\Listeners\Security;

use App\Events\Security\PasswordResetEvent;
use App\Notification\Security\PasswordResetEmail;

class PasswordResetNotifier
{
    public function __construct(
    ) {
        //
    }

    /**
     * Handle the event
     *
     * @param PasswordResetEvent $event
     * @return void
     */
    public function handle(PasswordResetEvent $event)
    {
        $event->user->notify(new PasswordResetEmail($event->user, $event->token, $event->expirationDate));
    }
}