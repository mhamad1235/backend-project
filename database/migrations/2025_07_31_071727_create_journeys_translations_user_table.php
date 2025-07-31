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
        Schema::create('journeys_translations_user', function (Blueprint $table) {
              $table->id();
              $table->foreignId('journey_id')->constrained()->onDelete('cascade'); 
              $table->string('locale')->index(); 
              $table->string('name');
              $table->text('description');
             $table->unique(['journey_id', 'locale']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journeys_translations_user');
    }
};
