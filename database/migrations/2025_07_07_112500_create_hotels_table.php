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
        Schema::create('hotels', function (Blueprint $table) {
 
            $table->id(); // Primary key
            $table->string('phone'); // Contact phone
            $table->decimal('latitude', 10, 7); // Latitude coordinates
            $table->decimal('longitude', 10, 7); // Longitude coordinates
            $table->foreignId('city_id')->constrained()->onDelete('cascade'); // Foreign key to cities
            $table->timestamps(); // created_at and updated_at
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
