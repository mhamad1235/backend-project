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
        Schema::create('journey_registration_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['family', 'group']);
            $table->unsignedSmallInteger('adults_count')->default(0);
            $table->unsignedSmallInteger('children_count')->default(0);
             $table->unsignedSmallInteger('total_people')->default(0);
            $table->boolean('paid')->default(false);
            $table->string('status')->default('pending'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journey_registration_groups');
    }
};
