<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability_calendar', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('residence_id', 10);
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->decimal('price_override', 10, 2)->nullable(); // prix spécial pour cette date
            $table->integer('min_stay')->nullable(); // séjour minimum pour cette date
            $table->timestamps();
            
            $table->unique(['residence_id', 'date']);
            $table->index(['date', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_calendar');
    }
};