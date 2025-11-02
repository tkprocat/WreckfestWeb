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
        Schema::create('track_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained('tracks')->onDelete('cascade');
            $table->string('variant_id')->unique(); // e.g., 'bigstadium_demolition_arena'
            $table->string('name'); // e.g., 'Demolition Arena'
            $table->boolean('is_derby')->default(false);
            $table->json('weather_conditions')->nullable(); // ['clear', 'overcast'] or null for all
            $table->json('tags')->nullable(); // ['oval', 'speedway', 'dirt', etc.]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_variants');
    }
};
