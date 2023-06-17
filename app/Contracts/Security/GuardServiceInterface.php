<?php

namespace App\Contracts\Security;

use App\EcoLearn\Models\User;

interface GuardServiceInterface
{
    /**
     * Index user accesses, email
     *
     * @param User $user
     * @return array
     */
    public function index(User $user): array;
}