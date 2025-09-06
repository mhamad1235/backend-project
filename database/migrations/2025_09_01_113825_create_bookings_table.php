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
       Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
        $table->foreignId('room_id')->constrained('hotel_rooms')->onDelete('cascade');
        $table->foreignId('unit_id')->constrained('hotel_room_units')->onDelete('cascade');
        $table->decimal('amount', 10, 2)->default(0);
        $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed'])->default('pending');
        $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
        $table->string('payment_method')->nullable();
        $table->string('transaction_id')->nullable();
        $table->date('booking_date')->nullable();
        $table->date('start_time');
        $table->date('end_time');
        $table->text('notes')->nullable();

        // Remove polymorphic fields
        // $table->unsignedBigInteger('bookable_id');
        // $table->string('bookable_type');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
