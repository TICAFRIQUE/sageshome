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
        // Supprimer d'abord la contrainte de clé étrangère
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_user_id_foreign');
        });
        
        // Modifier la table users pour utiliser des string IDs
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->string('id', 15)->primary()->first();
        });
        
        // Modifier la table bookings pour correspondre
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('user_id', 15)->change();
        });
        
        // Rétablir la contrainte de clé étrangère
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la contrainte de clé étrangère
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_user_id_foreign');
        });
        
        // Remettre la table users avec auto-increment
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->id()->first();
        });
        
        // Remettre la table bookings avec bigInteger
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });
        
        // Rétablir la contrainte de clé étrangère
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
