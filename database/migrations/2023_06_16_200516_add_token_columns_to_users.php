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
        Schema::table('users', function (Blueprint $table) {
            $table->string('token')->nullable()->after('email');
            $table->timestamp('token_valid_from')->nullable()->after('email');
            $table->timestamp('token_valid_till')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('token_valid_from');
            $table->dropColumn('token_valid_till');
        });
    }
};
