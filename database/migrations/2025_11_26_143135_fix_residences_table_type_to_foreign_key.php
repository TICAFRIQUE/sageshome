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
        // Vérifier si la colonne residence_type_id existe déjà
        if (Schema::hasColumn('residences', 'residence_type_id')) {
            // Si elle existe, juste remplir les données manquantes avec un type par défaut
            // On va assigner le type "Studio" (id: 1) à toutes les résidences avec type_id = 0 ou null
            DB::table('residences')->where('residence_type_id', 0)->orWhereNull('residence_type_id')->update(['residence_type_id' => 1]);
            
            Schema::table('residences', function (Blueprint $table) {
                // Supprimer la colonne enum type si elle existe
                if (Schema::hasColumn('residences', 'type')) {
                    $table->dropColumn('type');
                }
                
                // Ajouter la foreign key
                $table->foreign('residence_type_id')->references('id')->on('residence_types');
            });
        } else {
            // Si elle n'existe pas, la créer
            Schema::table('residences', function (Blueprint $table) {
                $table->unsignedBigInteger('residence_type_id')->nullable()->after('slug');
            });

            // Assigner un type par défaut à toutes les résidences
            DB::table('residences')->update(['residence_type_id' => 1]); // Studio par défaut
            
            Schema::table('residences', function (Blueprint $table) {
                // Supprimer la colonne enum type si elle existe
                if (Schema::hasColumn('residences', 'type')) {
                    $table->dropColumn('type');
                }
                
                // Rendre la colonne non nullable et ajouter la foreign key
                $table->unsignedBigInteger('residence_type_id')->nullable(false)->change();
                $table->foreign('residence_type_id')->references('id')->on('residence_types');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residences', function (Blueprint $table) {
            $table->dropForeign(['residence_type_id']);
            $table->dropColumn('residence_type_id');
            $table->enum('type', ['studio', 'studio_1ch', 'appartement_1ch', 'appartement_2ch', 'appartement_3ch', 'villa', 'penthouse']);
        });
    }
};
