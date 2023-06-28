<?php

namespace App\Contracts\Security;

use App\EcoLearn\Models\User;

interface PasswordServiceInterface
{
    /**
     * Verify token validity
     *
     * @param string $email
     * @param string $token
     * @return boolean
     */
    public function verifyToken(string $email, string $token): bool;

    /**
     * Undocumented function
     *
     * @param User $user
     * @return boolean
     */
    public function generatePasswordResetToken(User $user): bool;

    /**
     * Update password
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function setPassword(string $email, string $password): bool;
}