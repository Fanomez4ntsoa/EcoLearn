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
            CREATE TABLE `quizAnswers` (
                `answer_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` BIGINT(20) UNSIGNED NOT NULL,
                `quiz_id` BIGINT(20) UNSIGNED NOT NULL,
                `question_id` BIGINT(20) UNSIGNED NOT NULL,
                `chosen_option` VARCHAR(255) NOT NULL,
                PRIMARY KEY (`answer_id`) USING BTREE,
                INDEX `quizAnswers.user_id_foreign` (`user_id`) USING BTREE,
                INDEX `quizAnswers.quiz_id_foreign` (quiz_id) USING BTREE,
                INDEX `quizAnswers.question_id_foreign` (`question_id`) USING BTREE,
                CONSTRAINT `quizAnswers.user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
                CONSTRAINT `quizAnswers.quiz_id_foreign` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`),
                CONSTRAINT `quizAnswers.question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `quizQuestions` (`question_id`)
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
        DB::statement(' DROP TABLE IF EXISTS `quizAnswers` ');
    }
};
