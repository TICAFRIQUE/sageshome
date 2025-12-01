<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('booking_id', 10);
            $table->string('payment_reference')->unique();
            $table->enum('payment_method', ['wave', 'paypal', 'cash', 'bank_transfer']);
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('payment_details')->nullable(); // détails spécifiques au mode de paiement
            $table->string('transaction_id')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            // Champs Wave ajoutés directement
            $table->json('payment_data')->nullable(); // données JSON des API externes
            $table->timestamp('completed_at')->nullable(); // date de completion
            
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['booking_id']);
            $table->index(['status', 'payment_method']);
            
            // Contrainte de clé étrangère ajoutée directement
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};