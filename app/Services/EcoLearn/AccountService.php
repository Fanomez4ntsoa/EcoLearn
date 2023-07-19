<?php

namespace App\Services\EcoLearn;

use App\Contracts\EcoLearn\AccountServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountService implements AccountServiceInterface
{
    public function __construct(
    ) {
        // Do nothing
    }
    
    /**
     * Get profile for user
     *
     * @param string $accountId
     * @return string|null
     */
    public function getProfile(?string $profileId): ?string
    {
        $defaultProfileId = 1;

        if($profileId === null) {
            $profileId = $defaultProfileId;
        }
        try {
            $profile = DB::table('profiles')
                        ->where('profile_id', $profileId)
                        ->first();

            if(!$profile) {
                $profile = DB::table('profiles')
                            ->where('profile_id', $defaultProfileId)
                            ->first();
            };

            return $profile ? $profile->name : null;
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            return null;
        }
    }
}