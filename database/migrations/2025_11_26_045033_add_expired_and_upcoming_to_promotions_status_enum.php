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
        // Modify the enum to include 'expired' and 'upcoming'
        // MySQL doesn't support direct enum modification, so we use raw SQL
        DB::statement("ALTER TABLE `promotions` MODIFY COLUMN `status` ENUM('draft', 'active', 'archived', 'expired', 'upcoming') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        // Note: This will fail if there are any rows with 'expired' or 'upcoming' status
        // You may need to update those rows first before running down()
        DB::statement("ALTER TABLE `promotions` MODIFY COLUMN `status` ENUM('draft', 'active', 'archived') DEFAULT 'draft'");
    }
};
