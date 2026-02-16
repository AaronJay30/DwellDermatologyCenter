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
        Schema::create('medical_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('hypertension')->default(false);
            $table->boolean('diabetes')->default(false);
            $table->string('comorbidities_others')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->text('anesthetics')->nullable();
            $table->string('anesthetics_others')->nullable();
            $table->text('previous_hospitalizations_surgeries')->nullable();
            $table->enum('smoker', ['yes', 'no'])->nullable();
            $table->enum('alcoholic_drinker', ['yes', 'no'])->nullable();
            $table->text('known_family_illnesses')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_information');
    }
};
