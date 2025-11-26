<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\TrackCollection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'start_time' => fake()->dateTimeBetween('now', '+1 month'),
            'is_active' => false,
            'server_config' => null,
            'repeat' => null,
            'track_collection_id' => TrackCollection::factory(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the event is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_time' => fake()->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    /**
     * Indicate that the event has a server config.
     */
    public function withServerConfig(): static
    {
        return $this->state(fn (array $attributes) => [
            'server_config' => [
                'serverName' => fake()->words(2, true).' Server',
                'welcomeMessage' => fake()->sentence(),
                'maxPlayers' => fake()->numberBetween(8, 32),
            ],
        ]);
    }

    /**
     * Indicate that the event has a daily recurring pattern.
     */
    public function dailyRecurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'repeat' => [
                'frequency' => 'daily',
                'time' => fake()->time('H:i'),
            ],
        ]);
    }

    /**
     * Indicate that the event has a weekly recurring pattern.
     */
    public function weeklyRecurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'repeat' => [
                'frequency' => 'weekly',
                'days' => fake()->randomElements([0, 1, 2, 3, 4, 5, 6], fake()->numberBetween(1, 3)),
                'time' => fake()->time('H:i'),
            ],
        ]);
    }
}
