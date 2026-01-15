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
        Schema::table('promotions', function (Blueprint $table) {
            // Add title column (we'll keep name for backward compatibility, can remove later)
            if (!Schema::hasColumn('promotions', 'title')) {
                $table->string('title')->after('id');
            }
            
            // Change date fields to datetime
            $table->dateTime('starts_at')->nullable()->change();
            $table->dateTime('ends_at')->nullable()->change();
            
            // Add new fields
            if (!Schema::hasColumn('promotions', 'promo_code')) {
                $table->string('promo_code', 50)->nullable()->unique()->after('title');
            }
            if (!Schema::hasColumn('promotions', 'max_claims_per_patient')) {
                $table->unsignedInteger('max_claims_per_patient')->nullable()->after('promo_code');
            }
        });
        
        // Copy name to title if title is empty
        DB::statement("UPDATE promotions SET title = name WHERE title IS NULL OR title = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            if (Schema::hasColumn('promotions', 'title')) {
                $table->dropColumn('title');
            }
            $table->date('starts_at')->nullable()->change();
            $table->date('ends_at')->nullable()->change();
            if (Schema::hasColumn('promotions', 'promo_code')) {
                $table->dropColumn('promo_code');
            }
            if (Schema::hasColumn('promotions', 'max_claims_per_patient')) {
                $table->dropColumn('max_claims_per_patient');
            }
        });
    }
};
