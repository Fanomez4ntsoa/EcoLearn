<?php

namespace App\Events\User;

use App\Events\Event;

class UserCreatedEvent extends Event
{
    public function __construct(
        public string $user,
        public string $token
    ) {
        // Do nothing
    }
}