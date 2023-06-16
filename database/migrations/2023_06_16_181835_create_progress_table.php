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
            CREATE TABLE `progress` (
                `progress_id` BIGINT(20) UNSIGNED NOT NULL,
                `user_id` BIGINT(20) UNSIGNED NOT NULL,
                `ressource_id` BIGINT(20) UNSIGNED NOT NULL,
                `completed` BOOLEAN,
                PRIMARY KEY (`progress_id`) USING BTREE,
                INDEX `progress.user_id_foreign` (`user_id`) USING BTREE,
                INDEX `progress.ressource_id_foreign` (`ressource_id`) USING BTREE,
                CONSTRAINT `progress.user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
                CONSTRAINT `progress.ressource_id_foreign` FOREIGN KEY (`ressource_id`) REFERENCES `ressources` (`ressource_id`)
            )
            COLLATE='utf8mb4_unicode_ci'
            ENGINE=INNODB;
            ;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `progress` ');
    }
};
