<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('booking_number')->unique();
            $table->string('user_id', 15); // Utilisé string dès le début
            $table->string('residence_id', 10);
            
            // Informations client pour compatibilité avec les vues
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            
            // Dates de réservation
            $table->date('check_in');
            $table->date('check_out');
            // Alias pour les dates (compatibilité)
            $table->date('check_in_date')->nullable();
            $table->date('check_out_date')->nullable();
            
            // Informations séjour
            $table->integer('guests');
            $table->integer('guests_count'); // Alias pour compatibilité
            $table->integer('nights');
            
            // Montants
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('subtotal_amount', 10, 2); // Alias pour compatibilité
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->decimal('total_amount', 10, 2); // Alias pour compatibilité
            
            // Statuts
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'refunded'])->default('pending');
            
            // Notes et demandes
            $table->text('special_requests')->nullable();
            $table->text('notes')->nullable();
            
            // Timestamps de statut
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index et contraintes
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['residence_id']);
            $table->index(['check_in', 'check_out']);
            $table->index(['status', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};