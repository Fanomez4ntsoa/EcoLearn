<?php

namespace App\Contracts;

interface ActivityLoggerInterface
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
    public function dump( ?int $userId = null, string $comment, int $loginId = null, int $targetUserId = null, string $type = null);
}
