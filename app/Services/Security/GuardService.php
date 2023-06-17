<?php

namespace App\Services\Security;

use App\Contracts\Security\GuardServiceInterface;
use App\EcoLearn\Models\User;

class GuardService implements GuardServiceInterface
{
    /**
     * Index user accesses, email
     *
     * @param User $user
     * @return array
     */
    public function index(User $user): array
    {
        // Gestion des accès User :
        // Un accès pour les clients et un accès pour les admins
        return [];
    }
}