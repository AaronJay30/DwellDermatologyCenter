<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('available_doctor_image_path')->nullable()->after('image_path');
        });

        // Migrate existing available doctor images from image_path to available_doctor_image_path
        // Only if the image_path contains 'available-doctor' in the path
        DB::table('branches')
            ->whereNotNull('image_path')
            ->where('image_path', 'like', '%available-doctor%')
            ->update([
                'available_doctor_image_path' => DB::raw('image_path'),
                'image_path' => null
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('available_doctor_image_path');
        });
    }
};
