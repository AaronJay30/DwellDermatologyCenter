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
            // Drop the existing foreign key constraints
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['doctor_slot_id']);
            
            // Make fields nullable for consultation requests
            $table->unsignedBigInteger('doctor_id')->nullable()->change();
            $table->unsignedBigInteger('service_id')->nullable()->change();
            $table->unsignedBigInteger('doctor_slot_id')->nullable()->change();
            
            // Re-add the foreign key constraints with nullable
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('doctor_slot_id')->references('id')->on('doctor_slots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Drop the nullable foreign key constraints
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['doctor_slot_id']);
            
            // Make fields non-nullable again
            $table->unsignedBigInteger('doctor_id')->nullable(false)->change();
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
            $table->unsignedBigInteger('doctor_slot_id')->nullable(false)->change();
            
            // Re-add the non-nullable foreign key constraints
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('doctor_slot_id')->references('id')->on('doctor_slots')->onDelete('cascade');
        });
    }
};
