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
            $table->enum('type', ['studio_1ch', 'appartement_2ch', 'appartement_3ch']);
            $table->integer('capacity'); // nombre de voyageurs
            $table->decimal('price_per_night', 10, 2);
            $table->json('amenities')->nullable(); // équipements
            $table->string('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_available']);
            $table->index(['is_featured', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residences');
    }
};