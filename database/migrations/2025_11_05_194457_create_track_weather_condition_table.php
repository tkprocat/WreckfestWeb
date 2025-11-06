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
        Schema::create('track_weather_condition', function (Blueprint $table) {
            $table->foreignId('track_id')->constrained()->onDelete('cascade');
            $table->foreignId('weather_condition_id')->constrained()->onDelete('cascade');
            $table->primary(['track_id', 'weather_condition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_weather_condition');
    }
};
