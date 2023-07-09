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
            $table->unsignedBigInteger('ressource_id')->nullable()->after('quiz_id');
            $table->foreign('ressource_id')->references('ressource_id')->on('ressources');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizQuestions', function (Blueprint $table) {
            //
        });
    }
};
