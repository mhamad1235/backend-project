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
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('room_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('locale')->index(); 
            $table->string('name');           

            $table->unique(['room_type_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_type_translations');
        Schema::dropIfExists('room_types');
    }
};
