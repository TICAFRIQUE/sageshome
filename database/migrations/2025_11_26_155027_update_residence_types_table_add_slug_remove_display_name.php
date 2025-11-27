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
        Schema::table('residence_types', function (Blueprint $table) {
            // La colonne slug a déjà été ajoutée par le seeder
            // On supprime juste display_name
            $table->dropColumn('display_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residence_types', function (Blueprint $table) {
            $table->string('display_name')->after('name');
            $table->dropColumn('slug');
        });
    }
};
