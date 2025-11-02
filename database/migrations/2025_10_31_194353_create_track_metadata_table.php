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
        Schema::create('track_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('track_id')->unique(); // The variant ID from config (e.g., 'bigstadium_demolition_arena')
            $table->json('tags')->nullable(); // Tags like 'Dirt Road', 'City Streets', 'Crossroad', etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_metadata');
    }
};
