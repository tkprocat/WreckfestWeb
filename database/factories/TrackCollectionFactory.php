<?php

namespace Database\Factories;

use App\Models\TrackCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrackCollection>
 */
class TrackCollectionFactory extends Factory
{
    protected $model = TrackCollection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Collection',
            'tracks' => [
                [
                    'track' => 'loop',
                    'gamemode' => 'racing',
                    'laps' => 5,
                ],
            ],
        ];
    }

    /**
     * Indicate that the collection has multiple tracks.
     */
    public function withMultipleTracks(): static
    {
        return $this->state(fn (array $attributes) => [
            'tracks' => [
                ['track' => 'loop', 'gamemode' => 'racing', 'laps' => 5],
                ['track' => 'speedway2_figure_8', 'gamemode' => 'derby', 'laps' => 3],
                ['track' => 'sandpit', 'gamemode' => 'racing', 'laps' => 4],
            ],
        ]);
    }

    /**
     * Indicate that the collection is empty.
     */
    public function empty(): static
    {
        return $this->state(fn (array $attributes) => [
            'tracks' => [],
        ]);
    }
}
