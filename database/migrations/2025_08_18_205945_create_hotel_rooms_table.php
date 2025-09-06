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
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');       
            $table->unsignedInteger('guest'); 
            $table->unsignedInteger('bedroom');
            $table->unsignedInteger('beds');
            $table->unsignedInteger('bath');
            $table->unsignedInteger('quantity')->default(1); 
            $table->foreignId('room_type_id')->nullable() ->constrained('room_types')->nullOnDelete();
            $table->decimal('price', 10, 2)->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('hotel_rooms');
    }
};
