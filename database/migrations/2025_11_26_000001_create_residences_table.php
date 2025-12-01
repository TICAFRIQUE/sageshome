<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residences', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('full_description')->nullable();
            $table->string('residence_type_id', 10)->nullable(); // Référence vers residence_types
            $table->integer('capacity'); // nombre de voyageurs
            $table->decimal('price_per_night', 10, 2);
            $table->json('amenities')->nullable(); // équipements
            $table->string('address');
            
            // Champs de localisation ajoutés directement
            $table->string('ville');
            $table->string('commune')->nullable();
            
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
            
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['residence_type_id', 'is_available']);
            $table->index(['is_featured', 'is_available']);
            $table->index(['ville', 'commune']);
            $table->index('ville');
            
            // Contrainte de clé étrangère ajoutée directement
            $table->foreign('residence_type_id')->references('id')->on('residence_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residences');
    }
};