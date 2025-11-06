<?php

namespace Database\Seeders;

use App\Models\WeatherCondition;
use Illuminate\Database\Seeder;

class WeatherConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = ['clear', 'overcast', 'fog', 'rain', 'storm'];

        foreach ($conditions as $condition) {
            WeatherCondition::create(['name' => $condition]);
        }

        $this->command->info('Created '.count($conditions).' weather conditions');
    }
}
