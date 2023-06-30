<?php

namespace App\Services\Security;

use Illuminate\Support\Str;
use App\EcoLearn\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Contracts\User\UserServiceInterface;
use App\Contracts\Security\PasswordServiceInterface;
use App\Events\Security\PasswordResetEvent;
use App\Events\Security\PasswordSetEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PasswordService implements PasswordServiceInterface
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {
    }

    /**
     * Verify validity of the token
     *
     * @param string $email
     * @param string $token
     * @return boolean
     */
    public function verifyToken(string $email, string $token): bool
    {
        $user = $this->userService->findByEmail($email);
        if($user) {
            $hashedToken = $user->getPasswordToken();
            if(Hash::make($token) === $hashedToken) {
                return !is_expired($user->tokenValidFrom, $user->tokenValidTill);
            }
        }
        return false;
    }

    /**
     * Generate new password & Token
     *
     * @param User $user
     * @return boolean
     */
    public function generatePasswordResetToken(User $user): bool
    {
        $token = Str::random(30);
        $validFrom = Carbon::now();
        $validTill = Carbon::now()->addMinutes(config('ecoLearn.security.password.token.expiration'));

        try {
            $updated = DB::table('users')
                            ->where('email', $user->email)
                            ->update([
                                'token'                 => Hash::make($token),
                                'token_valid_from'      => $validFrom,
                                'token_valid_till'      => $validTill,
                            ]);
            if($updated) {
                event(new PasswordResetEvent($user, $token, $validTill));
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), $th->getTrace());
        }
    }

    /**
     * Set password
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function setPassword(string $email, string $password): bool
    {
        $user = $this->userService->findByEmail($email);
        $validFrom = Carbon::now();
        if($user) {
            $initialization = ($user->getHashedPassword() === '');
            DB::beginTransaction();
            try {
                DB::table('users')
                    ->where('user_id', $user->id)
                    ->update([
                        'password'              => Hash::make($password),
                        'token'                 => null,
                        'token_valid_from'      => $validFrom,
                        'token_valid_till'      => null,
                    ]);
                
                try {
                    event(new PasswordSetEvent($user, $initialization));
                } catch (\Throwable $th) {
                    Log::error($th->getMessage(), $th->getTrace());
                }
                DB::commit();
                
                return true;
            } catch (\Throwable $exception) {
                Log::error($exception->getMessage(), $exception->getTrace());
                DB::rollBack();
            }
        }

        return false;
    }
}