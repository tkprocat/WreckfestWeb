<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('track_variants', function (Blueprint $table) {
            // Add game_mode column as enum with Racing and Derby options
            $table->enum('game_mode', ['Racing', 'Derby'])->default('Racing')->after('name');
        });

        // Migrate existing is_derby data to game_mode
        DB::table('track_variants')
            ->where('is_derby', true)
            ->update(['game_mode' => 'Derby']);

        DB::table('track_variants')
            ->where('is_derby', false)
            ->update(['game_mode' => 'Racing']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('track_variants', function (Blueprint $table) {
            $table->dropColumn('game_mode');
        });
    }
};
