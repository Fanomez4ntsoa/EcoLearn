<?php

namespace App\Services\Security;

use App\Contracts\Security\GuardServiceInterface;
use App\EcoLearn\Models\User;
use Illuminate\Support\Facades\DB;

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
        $query = DB::table('profile_access')
            ->join('accessRight', 'accessRight.access_id', '=', 'profile_access.access_id')
            ->join('profiles', 'profiles.profile_id', '=', 'profile_access.profile_id')
            ->join('users', 'users.user_id', '=', 'profile_access.user_id')
            ->where('profile_access.user_id', $user->id);
            
        $query->distinct();

        $accesses = $query->get([
            'profile_access.access_id as id',
            'accessRight.key as access',
            'accessRight.name as name',
            'profile_access.user_id as user',
        ]);

        return [
            'email'     => $user->email,
            'accesses'  => $accesses,
        ];
    }
}