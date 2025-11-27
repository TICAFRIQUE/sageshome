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
            $table->unsignedBigInteger('user_id');
            $table->string('residence_id', 10);
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('guests');
            $table->integer('nights');
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'refunded'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
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