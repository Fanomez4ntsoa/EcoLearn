<?php

namespace App\Services\User;

use App\Contracts\User\UserServiceInterface;
use App\EcoLearn\Models\User;
use App\Models\Scopes\UnexpiredScope;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    /**
     * Find user by id
     *
     * @param integer $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        $user = DB::table('users')
                    ->where('user_id', $id)
                    ->where(function($query) {
                        (new UnexpiredScope())->applyToBuilder($query, 'users');
                    })
                    ->first();
        if($user) {
            $creationDate = to_datetime($user->created_at);
            $tokenValidFrom = to_datetime($user->Token_Valid_From);
            $tokenValidTill = to_datetime($user->Token_Valid_Till);

            $newUser = new User();
            $newUser->id            = $user->user_id;
            $newUser->username      = $user->username;
            $newUser->email         = $user->email;
            $newUser->created_at    = $creationDate;
            $newUser->tokenValidFrom= $tokenValidFrom;
            $newUser->tokenValidTill= $tokenValidTill;
            $newUser
                    ->setHashedPassword($user->password)
                    ->setPasswordToken($user->Token);

            return $newUser;
        }
        return null;
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        $user = DB::table('users')
                    ->where('email', $email)
                    ->where(function($query) {
                        (new UnexpiredScope())->applyToBuilder($query, 'users');
                    })
                    ->first();
        if($user) {
            $creationDate = to_datetime($user->created_at);
            $tokenValidFrom = to_datetime($user->Token_Valid_From);
            $tokenValidTill = to_datetime($user->Token_Valid_Till);

            $newUser = new User();
            $newUser->id            = $user->user_id;
            $newUser->username      = $user->username;
            $newUser->email         = $user->email;
            $newUser->created_at    = $creationDate;
            $newUser->tokenValidFrom= $tokenValidFrom;
            $newUser->tokenValidTill= $tokenValidTill;
            $newUser
                    ->setHashedPassword($user->password)
                    ->setPasswordToken($user->Token);

            return $newUser;
        }
        return null;
    }
}