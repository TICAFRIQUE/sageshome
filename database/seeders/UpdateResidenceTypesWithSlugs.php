<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateResidenceTypesWithSlugs extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // D'abord, ajoutons la colonne slug temporairement
        if (!DB::getSchemaBuilder()->hasColumn('residence_types', 'slug')) {
            DB::statement('ALTER TABLE residence_types ADD COLUMN slug VARCHAR(255) AFTER name');
        }

        // Récupérons tous les types existants
        $residenceTypes = DB::table('residence_types')->get();

        foreach ($residenceTypes as $type) {
            // Utilisons display_name pour créer le slug et mettre à jour name
            $name = $type->display_name ?? $type->name;
            $slug = Str::slug($name);
            
            // S'assurer que le slug est unique
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('residence_types')->where('slug', $slug)->where('id', '!=', $type->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Mettre à jour avec le name final et le slug
            DB::table('residence_types')
                ->where('id', $type->id)
                ->update([
                    'name' => $name,
                    'slug' => $slug
                ]);
        }
    }
}
