<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('residences', function (Blueprint $table) {
            // Ajouter la nouvelle colonne sans contrainte d'abord
            $table->unsignedBigInteger('residence_type_id')->nullable()->after('slug');
        });

        // Mapper les anciens types vers les nouveaux IDs
        DB::table('residences')->where('type', 'studio_1ch')->update(['residence_type_id' => 2]);
        DB::table('residences')->where('type', 'appartement_2ch')->update(['residence_type_id' => 4]);
        DB::table('residences')->where('type', 'appartement_3ch')->update(['residence_type_id' => 5]);
        
        Schema::table('residences', function (Blueprint $table) {
            // Supprimer la colonne enum type
            $table->dropColumn('type');
        });
        
        Schema::table('residences', function (Blueprint $table) {
            // Rendre la colonne non nullable et ajouter la foreign key
            $table->unsignedBigInteger('residence_type_id')->nullable(false)->change();
            $table->foreign('residence_type_id')->references('id')->on('residence_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residences', function (Blueprint $table) {
            // Supprimer la foreign key et la colonne
            $table->dropForeign(['residence_type_id']);
            $table->dropColumn('residence_type_id');
        });
        
        Schema::table('residences', function (Blueprint $table) {
            // Remettre l'enum
            $table->enum('type', ['studio_1ch', 'appartement_2ch', 'appartement_3ch'])->after('slug');
        });
    }
};
