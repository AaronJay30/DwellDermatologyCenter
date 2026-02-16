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
            $table->text('consultation_result')->nullable()->after('appointment_id');
            $table->boolean('follow_up_required')->default(false)->after('prescription');
            $table->date('follow_up_date')->nullable()->after('follow_up_required');
            $table->text('notes')->nullable()->after('follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_histories', function (Blueprint $table) {
            $table->dropColumn(['consultation_result', 'follow_up_required', 'follow_up_date', 'notes']);
        });
    }
};