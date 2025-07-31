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
        Schema::create('unavailable_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bookable_id');
            $table->string('bookable_type');

            $table->date('unavailable_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->timestamps();

            $table->index(['bookable_id', 'bookable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unavailable_slots');
    }
};
