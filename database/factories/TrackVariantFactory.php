<?php

namespace Database\Factories;

use App\Models\Track;
use App\Models\TrackVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrackVariant>
 */
class TrackVariantFactory extends Factory
{
    protected $model = TrackVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'track_id' => Track::factory(),
            'variant_id' => $this->faker->unique()->slug(2),
            'name' => $this->faker->words(2, true),
            'game_mode' => $this->faker->randomElement(['Racing', 'Derby']),
            'weather_conditions' => null,
        ];
    }
}
