<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residence_images', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('residence_id', 10);
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['residence_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residence_images');
    }
};