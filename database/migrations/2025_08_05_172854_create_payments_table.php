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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('paymentable'); // creates paymentable_id + paymentable_type
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('fib_payment_id')->nullable(); // FIB payment reference
            $table->unsignedBigInteger('amount'); // store 10000 directly
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->json('fib_response')->nullable(); // optional: full FIB response
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
