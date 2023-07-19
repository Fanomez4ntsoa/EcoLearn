<?php 

namespace App\Listeners\User;

use App\Events\User\UserCreatedEvent;
use App\Notification\User\ValidationAccountEmail;

class SendUserCreated
{
    public function __construct(
    ) {
        //
    }

    /**
     * Handle the event
     *
     * @param UserCreatedEvent $event
     * @return void
     */
    public function handle(UserCreatedEvent $event)
    {
        $event->user->notify(new ValidationAccountEmail($event->token));
    }
}