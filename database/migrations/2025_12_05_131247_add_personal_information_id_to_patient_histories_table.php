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
        Schema::table('patient_histories', function (Blueprint $table) {
            $table->foreignId('personal_information_id')->nullable()->after('patient_id')->constrained('personal_information')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_histories', function (Blueprint $table) {
            $table->dropForeign(['personal_information_id']);
            $table->dropColumn('personal_information_id');
        });
    }
};
