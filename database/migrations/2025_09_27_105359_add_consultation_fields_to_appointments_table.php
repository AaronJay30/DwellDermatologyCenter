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
            $table->string('first_name')->nullable();
            $table->string('middle_initial', 1)->nullable();
            $table->string('last_name')->nullable();
            $table->integer('age')->nullable();
            $table->string('consultation_type')->nullable();
            $table->text('description')->nullable();
            $table->text('medical_background')->nullable();
            $table->string('referral_source')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('time_slot_id')->nullable()->constrained('time_slots')->onDelete('cascade');
            $table->string('cancellation_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['time_slot_id']);
            $table->dropColumn([
                'first_name',
                'middle_initial', 
                'last_name',
                'age',
                'consultation_type',
                'description',
                'medical_background',
                'referral_source',
                'branch_id',
                'time_slot_id',
                'cancellation_reason'
            ]);
        });
    }
};