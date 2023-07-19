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
            CREATE TABLE `comments` (
                `comment_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT(20) UNSIGNED NOT NULL,
                `ressource_id` BIGINT(20) UNSIGNED NOT NULL,
                `comment_text` TEXT,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`comment_id`) USING BTREE,
                INDEX `comments_user_id_foreign` (`user_id`) USING BTREE,
                INDEX `comments_ressource_id_foreign` (`ressource_id`) USING BTREE,
                CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
                CONSTRAINT `comments_ressource_id_foreign` FOREIGN KEY (`ressource_id`) REFERENCES `ressources` (`ressource_id`)
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
        DB::statement('DROP TABLE IF EXISTS `comments`');
    }
};
