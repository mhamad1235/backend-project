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
        Schema::create('room_availabilities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('hotel_room_unit_id')->constrained('hotel_room_units')->cascadeOnDelete();
        $table->date('date'); 
        $table->boolean('available')->default(true); 
        $table->timestamps();
        $table->unique(['hotel_room_unit_id', 'date']); 
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_availabilities');
    }
};
