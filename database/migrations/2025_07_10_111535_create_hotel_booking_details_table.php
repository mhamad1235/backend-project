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
        Schema::create('hotel_booking_details', function (Blueprint $table) {
             $table->id();
             $table->foreignId('booking_id')->constrained()->onDelete('cascade');
             $table->integer('rooms');
             $table->integer('adults');
             $table->integer('children');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_details');
    }
};
