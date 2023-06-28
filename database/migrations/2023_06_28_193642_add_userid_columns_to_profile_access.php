<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE TABLE `profile_access` (
                `profile_id` BIGINT(20) UNSIGNED NOT NULL,
                `user_id` BIGINT(20) UNSIGNED NOT NULL,
                `access_id` BIGINT(20) UNSIGNED NOT NULL,
                INDEX `profile_access.profile_id_foreign` (`profile_id`) USING BTREE,
                INDEX `profile_access.user_id_foreign` (`user_id`) USING BTREE,
                INDEX `profile_access.access_id_foreign` (`access_id`) USING BTREE,
                CONSTRAINT `profile_access.access_id_foreign` FOREIGN KEY (`access_id`) REFERENCES `accessRight` (`access_id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
                CONSTRAINT `profile_access.profile_id_foreign` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`) ON UPDATE RESTRICT ON DELETE RESTRICT,
                CONSTRAINT `profile_access.user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE RESTRICT ON DELETE RESTRICT
            )
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=InnoDB
            ;
        ");
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(' DROP TABLE IF EXISTS `profile_access` ');
    }
};
