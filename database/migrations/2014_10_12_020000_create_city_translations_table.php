<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("city_id")->constrained()->cascadeOnDelete();
            $table->string("name", 20);
            $table->string('locale', 2)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['city_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_translations');
    }
};
