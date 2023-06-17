<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
                `access_id` BIGINT(20) UNSIGNED NOT NULL,
                INDEX `profile_access.profile_id_foreign` (`profile_id`) USING BTREE,
                INDEX `profile_access.access_id_foreign` (`access_id`) USING BTREE,
                CONSTRAINT `profile_access.profile_id_foreign` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`),
                CONSTRAINT `profile_access.access_id_foreign` FOREIGN KEY (`access_id`) REFERENCES `accessRight` (`access_id`)
            )
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=INNODB;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(" DROP TABLE IF EXISTS `profile_access` ");
    }
};
