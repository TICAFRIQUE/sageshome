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
        Schema::create('residence_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // studio, appartement_1ch, appartement_2ch, villa, etc.
            $table->string('display_name'); // Nom d'affichage : "Studio", "Appartement 1 chambre", etc.
            $table->text('description')->nullable();
            $table->integer('min_capacity')->default(1);
            $table->integer('max_capacity')->default(10);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residence_types');
    }
};
