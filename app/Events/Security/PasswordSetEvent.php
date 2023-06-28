<?php

namespace App\Events\Security;

use App\EcoLearn\Models\User;
use App\Events\Event;

class PasswordSetEvent extends Event
{
    /**
     * Create a new event instance
     *
     * @param User $user
     * @param boolean $initialization
     */
    public function __construct(
        public User $user, 
        public bool $initialization
    ) {
        //
    }
}
