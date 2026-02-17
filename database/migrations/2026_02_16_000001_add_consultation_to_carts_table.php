<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('carts', 'item_type')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->string('item_type', 20)->default('service')->after('user_id');
            });
        }
        if (!Schema::hasColumn('carts', 'branch_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('service_id')->constrained()->nullOnDelete();
            });
        }
        // Make service_id nullable (MySQL)
        DB::statement('ALTER TABLE carts MODIFY service_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (Schema::hasColumn('carts', 'branch_id')) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
            });
        }
        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'item_type')) $table->dropColumn('item_type');
            if (Schema::hasColumn('carts', 'branch_id')) $table->dropColumn('branch_id');
        });
        DB::statement('ALTER TABLE carts MODIFY service_id BIGINT UNSIGNED NOT NULL');
    }
};
