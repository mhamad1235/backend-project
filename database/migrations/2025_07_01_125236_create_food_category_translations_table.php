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
        Schema::create('food_category_translations', function (Blueprint $table) {
             $table->id();
            $table->foreignId('food_category_id')->constrained('food_categories')->cascadeOnDelete();
            $table->string('locale')->index(); 
            $table->string('name');
            $table->timestamps();

            $table->unique(['food_category_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_category_translations');
    }
};
