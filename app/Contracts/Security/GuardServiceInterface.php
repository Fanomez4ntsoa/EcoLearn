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

    /**
     * Check user access
     *
     * @param User $user
     * @param string $access
     * @return boolean
     */
    public function allows(User $user, string $access): bool;
}