<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizQuestions', function (Blueprint $table) {
            $table->json('answer_possibilities')->nullable();
            $table->string('correct_option')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizQuestions', function (Blueprint $table) {
            $table->dropColumn('answer_possibilities');
            $table->dropColumn('correct_option');
        });
    }
};
