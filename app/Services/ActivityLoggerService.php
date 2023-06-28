<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityLoggerService implements ActivityLoggerInterface
{
    /**
     * Log user activity
     *
     * @param string $comment
     * @param integer|null $userId
     * @param integer|null $loginId
     * @param integer|null $groupId
     * @param integer|null $targetUserId
     * @param string|null $type
     * @return void
     */
    public function dump(?int $userId = null, string $comment, ?int $emailId = null, ?int $targetUserId = null, ?string $type = null)
    {
        $now = Carbon::now();
        $ip = request()->ip();

        $data = [
            'user_id'           => $userId,
            'email_id'          => $emailId,
            'user_comment'      => $comment,
            'activityDate'      => $now,
            'user_IPAddress'    => $ip,
            'targetUser_Id'     => $targetUserId,
            'activityType_Key'  => $type,

        ];

        DB::table('userActivity')
                ->insertOrIgnore(Arr::whereNotNull($data));
    }
}