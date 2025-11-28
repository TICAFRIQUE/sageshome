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
        Schema::table('residences', function (Blueprint $table) {
            $table->string('ville')->after('address');
            $table->string('commune')->nullable()->after('ville');
            $table->string('google_maps_url')->nullable()->after('longitude');
            
            // Ajouter des index pour optimiser les recherches
            $table->index(['ville', 'commune']);
            $table->index('ville');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residences', function (Blueprint $table) {
            $table->dropIndex(['ville', 'commune']);
            $table->dropIndex(['ville']);
            $table->dropColumn(['ville', 'commune', 'google_maps_url']);
        });
    }
};
