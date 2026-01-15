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
        Schema::table('appointments', function (Blueprint $table) {
            // Modify the status enum to include 'booked'
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show', 'pending', 'booked'])->default('scheduled')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Revert the status enum to remove 'booked'
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show', 'pending'])->default('scheduled')->change();
        });
    }
};
