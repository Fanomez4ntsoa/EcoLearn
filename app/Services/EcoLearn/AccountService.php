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

        // Si aucun ID de profil n'est fourni, utiliser le profil par défaut (ADMINISTRATION_CLIENT)
        if($profileId === null) {
            $profileId = $defaultProfileId;
        }
        try {
            $profile = DB::table('profiles')
                        ->where('profile_id', $profileId)
                        ->first();

            if(!$profile) {
                // Si le profil n'est pas trouvé, utilisez à nouveau le profil par défaut
                $profile = DB::table('profiles')
                            ->where('profile_id', $defaultProfileId)
                            ->first();
            };

            return $profile ? $profile->name : '';
            
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [$th]);
            return '';
        }
    }
}