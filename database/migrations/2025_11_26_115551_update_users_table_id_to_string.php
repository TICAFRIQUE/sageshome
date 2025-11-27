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
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la clé primaire auto-increment
            $table->dropPrimary();
            $table->dropColumn('id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // Ajouter la nouvelle colonne id string
            $table->string('id', 15)->primary()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->id()->first();
        });
    }
};
