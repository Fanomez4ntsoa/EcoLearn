<?php

namespace App\Contracts\User;

use App\EcoLearn\Models\User;
use Illuminate\Support\Collection;

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

    /**
     * All users Client who is not Admin
     *
     * @param User $user
     * @return Collection
     */
    public function getAllclientUser(): Collection;

    /**
     * Create new Client user EcoLearn
     *
     * @param string $email
     * @param string $name
     * @param string $username
     * @return integer
     */
    public function create(string $email, string $name, string $username, ?string $profileId): int;

    /**
     * Update user
     *
     * @param string $email
     * @param string $name
     * @param string $username
     * @return boolean
     */
    public function update(User $user, string $email, string $name, string $username): bool;

    /**
     * Delete user
     *
     * @param User $user
     * @return boolean
     */
    public function delete(User $user): bool;

}