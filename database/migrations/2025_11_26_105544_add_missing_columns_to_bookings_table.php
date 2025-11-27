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
        Schema::table('bookings', function (Blueprint $table) {
            // Ajouter les colonnes manquantes pour compatibilité avec les vues
            $table->string('first_name')->nullable()->after('user_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('country')->nullable()->after('phone');
            
            // Alias pour les dates
            $table->date('check_in_date')->nullable()->after('check_in');
            $table->date('check_out_date')->nullable()->after('check_out');
            
            // Alias pour les montants
            $table->integer('guests_count')->after('guests');
            $table->decimal('subtotal_amount', 10, 2)->after('total_price');
            $table->decimal('total_amount', 10, 2)->after('final_amount');
        });
        
        // Copier les données existantes dans les nouveaux champs
        DB::statement('UPDATE bookings SET check_in_date = check_in, check_out_date = check_out, guests_count = guests, subtotal_amount = total_price, total_amount = final_amount');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'email', 'phone', 'country',
                'check_in_date', 'check_out_date', 'guests_count',
                'subtotal_amount', 'total_amount'
            ]);
        });
    }
};
