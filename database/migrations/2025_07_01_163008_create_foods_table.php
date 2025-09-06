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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
        $table->string('name'); // you can keep for default / fallback or move to translations
        $table->decimal('price', 8, 2);
        $table->text('description')->nullable();
        $table->boolean('is_available')->default(true);
        $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
       $table->foreignId('category_id')->constrained('food_categories')->onDelete('cascade');// new relation
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
