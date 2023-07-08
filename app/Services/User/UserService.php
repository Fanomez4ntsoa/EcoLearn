<?php

namespace App\Services\User;

use App\Contracts\EcoLearn\AccountServiceInterface;
use App\Contracts\User\UserServiceInterface;
use App\EcoLearn\Models\User;
use App\Events\User\UserCreatedEvent;
use Illuminate\Support\Str;
use App\Models\Scopes\UnexpiredScope;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected AccountServiceInterface $accountService
    ) {
        //
    }
    
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
            $creationDate   = to_datetime($user->created_at);
            $tokenValidFrom = to_datetime($user->token_valid_from);
            $tokenValidTill = to_datetime($user->token_valid_till);

            $newUser = new User();
            $newUser->id            = $user->user_id;
            $newUser->name          = $user->name;
            $newUser->username      = $user->username;
            $newUser->email         = $user->email;
            $newUser->role          = $user->isAdmin;
            $newUser->created_at    = $creationDate;
            $newUser->tokenValidFrom= $tokenValidFrom;
            $newUser->tokenValidTill= $tokenValidTill;
            $newUser
                    ->setHashedPassword($user->password)
                    ->setPasswordToken($user->token);

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
            $tokenValidFrom = to_datetime($user->token_valid_from);
            $tokenValidTill = to_datetime($user->token_valid_till);

            $newUser = new User();
            $newUser->id            = $user->user_id;
            $newUser->username      = $user->username;
            $newUser->email         = $user->email;
            $newUser->created_at    = $creationDate;
            $newUser->tokenValidFrom= $tokenValidFrom;
            $newUser->tokenValidTill= $tokenValidTill;
            $newUser
                    ->setHashedPassword($user->password)
                    ->setPasswordToken($user->token);

            return $newUser;
        }
        return null;
    }

    /**
     * User index
     *
     * @param string|null $field
     * @param string|null $search
     * @param integer|null $perPage
     * @return Paginator
     */
    public function index(string $field = null, string $search = null, int $perPage = null ): Paginator
    {
        $query = DB::table('users')
                    ->whereNull('isAdmin')
                    ->where(function($query) {
                        (new UnexpiredScope())->applyToBuilder($query, 'users');
                    })
                    ->where(function($query) use ($field, $search) {
                        $maps = [
                            'id'        => 'user_id',
                            'name'      => 'name',
                            'username'  => 'username',
                            'email'     => 'email',
                        ];

                        if($search) {
                            if(is_null($field) || $field === '') {
                                $compare = '%' . Str::replaceArray(' ', ['%', ''], $search) . '%';
                                $query
                                    ->where('name', 'like', $compare)
                                    ->orWhere('username', 'like', $compare)
                                    ->orWhere('email', 'like', $compare);
                            } else if(isset($maps[$field])) {
                                $query->where($maps[$field], 'like', '%' . Str::replace(' ', '%', $search) . '%');
                            }
                        }

                    });

        return $query->paginate($perPage);
    }

    /**
     * Create new Client user EcoLearn
     *
     * @param string $email
     * @param string $name
     * @param string $username
     * @return integer
     */
    public function create(string $email, string $name, string $username, ?string $profileId): int
    {
        $userProfile = $this->accountService->getProfile($profileId);
        if(!$userProfile) {
            return ERROR_PROFILE_UNDEFINED;
        }

        $now = Carbon::now();
        $token = Str::random(30);
        $tokenValidFrom = $now;

        DB::beginTransaction();
        try {
            $userExists = DB::table('users')
                    ->where('email', $email)
                    ->exists();
                    
            // Initialisation à true par défaut
            $newUser = true;
            
            if($userExists) {
                $user = DB::table('users')
                    ->where('email', $email)
                    ->first();

                $userId = $user->user_id;
                DB::table('users')
                    ->where('user_id', $userId)
                    ->update([
                        'Valid_From'    => $now,
                        'Valid_Till'    => null
                    ]);
                
                // Utilisateur existant, donc $newUser est false
                $newUser = false;

            } else {
                if($userProfile === 'ADMINISTRATION_ADMIN') {
                    $userId = DB::table('users')
                                ->insertGetId([
                                    'name'              => $name,
                                    'username'          => $username,
                                    'email'             => strtolower($email),
                                    'Valid_From'        => $now,
                                    'password'          => '',
                                    'isAdmin'           => 1,
                                    'token'             => Hash::make($token),
                                    'token_valid_from'  => $tokenValidFrom,
                                    'created_at'        => $now
                            ]);
                } else {
                    $userId = DB::table('users')
                                ->insertGetId([
                                    'name'              => $name,
                                    'username'          => $username,
                                    'email'             => strtolower($email),
                                    'Valid_From'        => $now,
                                    'password'          => '',
                                    'isAdmin'           => null,
                                    'token'             => Hash::make($token),
                                    'token_valid_from'  => $tokenValidFrom,
                                    'created_at'        => $now
                        ]);
                }
            }

            $profileId = DB::table('profiles')
                            ->where('name', $userProfile)
                            ->first()
                            ->profile_id ?? null;

            if($profileId) {
                if($profileId == 1) {
                    $accessList = [
                        ACCESS_CLIENT_USER          => '1',
                        ACCESS_CLIENT_RESOURCE      => '2',
                        ACCESS_CLIENT_COMMENTAIRE   => '3',
                        ACCESS_CLIENT_PROGRESS      => '4',
                        ACCESS_CLIENT_STATISTIQUE   => '5'
                    ];
                } else if($profileId != 1) {
                    $accessList = [
                        ACCESS_CLIENT_USER              => '1',
                        ACCESS_CLIENT_RESOURCE          => '2',
                        ACCESS_CLIENT_COMMENTAIRE       => '3',
                        ACCESS_CLIENT_PROGRESS          => '4',
                        ACCESS_CLIENT_STATISTIQUE       => '5',
                        ACCESS_ADMIN_PROFILES_ACCESS    => '6',
                        ACCESS_ADMIN_CATEGORIES         => '7',
                        ACCESS_ADMIN_BADGE              => '8',
                        ACCESS_ADMIN_QUIZ               => '9',
                        ACCESS_ADMIN_STATISTIQUE        => '10',
                        ACCESS_ADMIN_USER               => '16'
                    ];
                }
                
                foreach ($accessList as $accessId) {
                    try {
                        DB::table('profile_access')
                            ->insert([
                                'profile_id'    => $profileId,
                                'user_id'       => $userId,
                                'access_id'     => $accessId
                            ]);

                        
                    } catch (\Throwable $th) {
                        Log::error($th->getMessage(), [$th]);
                    }
                }
                
                $accesses = DB::table('profile_access')
                                ->join('accessRight', 'accessRight.access_id', '=', 'profile_access.access_id')
                                ->join('users', 'users.user_id', '=', 'profile_access.user_id')
                                ->where('profile_id', $profileId)
                                ->get();

                $userExists = $this->find($userId);
                if($accesses) {
                    if($newUser) {
                        event(new UserCreatedEvent($userExists, $token));
                    }
                }

                DB::commit();
                return SUCCESS_USER_CREATED;
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return ERROR_USER_CREATED;
        }
    }

    /**
     * Update user
     *
     * @param string $email
     * @param string $name
     * @param string $username
     * @return boolean
     */
    public function update(User $user, string $email, string $name, string $username): bool
    {
        DB::beginTransaction();
        try {
            DB::table('users')
                ->where('user_id', $user->id)
                ->update([
                    'name'      => $name,
                    'username'  => $username,
                    'email'     => $email
                ]);

            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), [$th]);

            return false;
        }
    }

    /**
     * Delete user
     *
     * @param User $user
     * @return boolean
     */
    public function delete(User $user): bool
    {
        DB::beginTransaction();
        try {
            DB::table('profile_access')
                ->where('user_id', $user->id)
                ->delete();

            $isDeleted = DB::table('users')
                            ->where('user_id', $user->id)
                            ->delete();

            DB::commit();
            return $isDeleted;

        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
        }
    }
}