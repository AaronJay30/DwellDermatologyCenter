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
        Schema::table('personal_information', function (Blueprint $table) {
            $table->string('civil_status', 20)->nullable()->after('birthday');
            $table->string('preferred_pronoun', 50)->nullable()->after('civil_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_information', function (Blueprint $table) {
            $table->dropColumn(['civil_status', 'preferred_pronoun']);
        });
    }
};


