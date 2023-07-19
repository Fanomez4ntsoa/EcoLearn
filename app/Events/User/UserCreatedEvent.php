<?php

namespace App\Events\User;

use App\EcoLearn\Models\User;
use App\Events\Event;

class UserCreatedEvent extends Event
{
    /**
     * Create a new event instance
     *
     * @param User $user
     * @param string $token
     */
    public function __construct(
        public User $user,
        public string $token
    ) {
        // Do nothing
    }
}