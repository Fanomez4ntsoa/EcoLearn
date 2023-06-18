<?php

namespace App\Contracts\EcoLearn;

interface AccountServiceInterface
{
    /**
     * Get profile for user
     *
     * @param string $accountId
     * @param string $group
     * @return string|null
     */
    public function getProfile(string $email, ?string $profileId): ?string;
}