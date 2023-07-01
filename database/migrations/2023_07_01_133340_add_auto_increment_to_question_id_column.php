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
            // $table->dropPrimary('PRIMARY');
            // $table->bigIncrements('question_id')->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizQuestions', function (Blueprint $table) {
            // Do nothing
        });
    }
};
