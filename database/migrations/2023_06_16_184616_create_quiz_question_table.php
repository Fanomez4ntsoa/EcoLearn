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
            CREATE TABLE `quizQuestions` (
                `question_id` BIGINT(20) UNSIGNED NOT NULL,
                `quiz_id` BIGINT(20) UNSIGNED NOT NULL,
                `question_text` VARCHAR(255),
                PRIMARY KEY (`question_id`) USING BTREE,
                INDEX `quizQuestions.quiz_id_foreign` (`quiz_id`) USING BTREE,
                CONSTRAINT `quizQuestions.quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`)
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
        DB::statement('DROP TABLE IF EXISTS `quizQuestions` ');
    }
};
