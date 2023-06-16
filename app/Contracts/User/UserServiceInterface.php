<?php

namespace App\Contracts\User;

use App\EcoLearn\Models\User;

interface UserServiceInterface
{
    /**
     * Find user by id
     *
     * @param integer $id
     * @return User|null
     */
    public function find(int $id): ?User;

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;
}