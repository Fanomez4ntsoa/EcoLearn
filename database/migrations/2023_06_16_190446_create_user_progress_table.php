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
            CREATE TABLE `userProgress` (
                `progress_id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` BIGINT UNSIGNED,
                `category_id` BIGINT UNSIGNED,
                `progress_percentage` INT,
                INDEX `userProgress_user_id_foreign` (`user_id`) USING BTREE,
                INDEX `userProgress_category_id_foreign` (`category_id`) USING BTREE,
                CONSTRAINT `userProgress_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT `userProgress_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE
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
        DB::statement(' DROP IF TABLE EXISTS `userProgress` ');
    }
};
