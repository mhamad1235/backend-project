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
        Schema::create('journey_user', function (Blueprint $table) {
              $table->id();
              $table->foreignId('journey_id')->constrained()->onDelete('cascade');
              $table->foreignId('user_id')->constrained()->onDelete('cascade');
              $table->boolean('paid')->default(false); // to track payment status
              $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journey_user');
    }
};
