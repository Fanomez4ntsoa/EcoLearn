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
            CREATE TABLE `userActivity` (
                `activity_Id` INT(11) NOT NULL AUTO_INCREMENT,
                `user_id` INT(11) NULL DEFAULT NULL,
                `email_id` INT(11) NULL DEFAULT NULL,
                `user_comment` TEXT NOT NULL COLLATE 'utf8_general_ci',
                `activityDate` DATETIME NOT NULL,
                `user_IPAddress` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                `targetUser_Id` INT(11) NULL DEFAULT NULL,
                `activityType_Key` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
                PRIMARY KEY (`activity_Id`) USING BTREE,
                INDEX `fk_userActivity_user1` (`user_id`) USING BTREE,
                INDEX `fk_userActivity_email1` (`email_id`) USING BTREE,
                INDEX `i_userActivity_Date` (`activityDate`) USING BTREE
            )
            COMMENT='Decrit les evenements survenus dans l\'application'
            COLLATE='utf8_general_ci'
            ENGINE=INNODB;
            ;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(' DROP TABLE IF EXISTS `userActivity` ');
    }
};
