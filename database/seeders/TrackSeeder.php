<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\TrackMetadata;
use App\Models\TrackVariant;
use App\Models\WeatherCondition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Track data - moved from config/wreckfest.php
        $trackLocations = $this->getTrackData();

        // Get existing tag data from TrackMetadata if table exists
        $existingTags = [];
        if (Schema::hasTable('track_metadata')) {
            $metadata = TrackMetadata::all();
            foreach ($metadata as $meta) {
                $existingTags[$meta->track_id] = $meta->tags ?? [];
            }
        }

        $this->command->info('Importing '.count($trackLocations).' track locations...');

        foreach ($trackLocations as $locationKey => $location) {
            $locationName = $location['name'] ?? $locationKey;
            $variants = $location['variants'] ?? [];
            $weather = $location['weather'] ?? null;

            // Create the track location
            $track = Track::create([
                'key' => $locationKey,
                'name' => $locationName,
            ]);

            $this->command->info("Created track: {$locationName}");

            // Attach weather conditions if specified
            if ($weather !== null && is_array($weather)) {
                $weatherIds = WeatherCondition::whereIn('name', $weather)->pluck('id');
                $track->weatherConditions()->attach($weatherIds);
                $this->command->info("  Attached ".count($weather)." weather conditions");
            } elseif ($weather === null) {
                // null means all weather conditions are supported
                $allWeatherIds = WeatherCondition::pluck('id');
                $track->weatherConditions()->attach($allWeatherIds);
                $this->command->info("  Attached all weather conditions (DLC track)");
            }

            // Create each variant
            foreach ($variants as $variantId => $variant) {
                $variantName = is_array($variant) ? ($variant['name'] ?? $variantId) : $variant;
                $isDerby = is_array($variant) ? ($variant['derby'] ?? false) : false;
                $gameMode = $isDerby ? 'Derby' : 'Racing';

                TrackVariant::create([
                    'track_id' => $track->id,
                    'variant_id' => $variantId,
                    'name' => $variantName,
                    'game_mode' => $gameMode,
                    'weather_conditions' => $weather, // Keep for backwards compatibility
                ]);
            }

            $this->command->info("  Added ".count($variants)." variants");
        }

        $totalVariants = TrackVariant::count();
        $this->command->info("Import complete! Created ".Track::count()." tracks with {$totalVariants} variants.");
    }

    /**
     * Get all track data
     */
    protected function getTrackData(): array
    {
        return [
            'madman_stadium' => [
                'name' => 'Madman Stadium',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'bigstadium_demolition_arena' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                    'bigstadium_figure_8' => [
                        'name' => 'Figure 8',
                        'derby' => false,
                    ],
                ],
            ],
            'fairfield_county' => [
                'name' => 'Fairfield County',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                    2 => 'fog',
                ],
                'variants' => [
                    'smallstadium_demolition_arena' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'fairfield_mudpit' => [
                'name' => 'Fairfield Mud Pit',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                    2 => 'fog',
                ],
                'variants' => [
                    'mudpit_demolition_arena' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'fairfield_grass_field' => [
                'name' => 'Fairfield Grass Field',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                    2 => 'fog',
                ],
                'variants' => [
                    'grass_arena_demolition_arena' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'glendale_countryside' => [
                'name' => 'Glendale Countryside',
                'weather' => [
                    0 => 'overcast',
                ],
                'variants' => [
                    'field_derby_arena' => [
                        'name' => 'Field Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'bloomfield_speedway' => [
                'name' => 'Bloomfield Speedway',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'dirt_speedway_dirt_oval' => [
                        'name' => 'Dirt Oval',
                        'derby' => false,
                    ],
                    'dirt_speedway_figure_8' => [
                        'name' => 'Figure 8',
                        'derby' => false,
                    ],
                ],
            ],
            'bonebreaker_valley' => [
                'name' => 'Bonebreaker Valley',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'bonebreaker_valley_main_circuit' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                ],
            ],
            'crash_canyon' => [
                'name' => 'Crash Canyon',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'crash_canyon_main_circuit' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                ],
            ],
            'midwest_motocenter' => [
                'name' => 'Midwest Motocenter',
                'weather' => [
                    0 => 'overcast',
                ],
                'variants' => [
                    'gravel1_main_loop' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'gravel1_main_loop_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'hilltop_stadium' => [
                'name' => 'Hilltop Stadium',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                    2 => 'fog',
                ],
                'variants' => [
                    'speedway1_figure_8' => [
                        'name' => 'Figure 8',
                        'derby' => false,
                    ],
                    'speedway1_oval' => [
                        'name' => 'Oval',
                        'derby' => false,
                    ],
                ],
            ],
            'big_valley_speedway' => [
                'name' => 'Big Valley Speedway',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'speedway2_classic_arena' => [
                        'name' => 'Open Demolition Arena',
                        'derby' => true,
                    ],
                    'speedway2_demolition_arena' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                    'speedway2_figure_8' => [
                        'name' => 'Figure 8',
                        'derby' => false,
                    ],
                    'speedway2_inner_oval' => [
                        'name' => 'Inner Oval',
                        'derby' => false,
                    ],
                    'speedway2_outer_oval' => [
                        'name' => 'Outer Oval',
                        'derby' => false,
                    ],
                    'speedway2_oval_loop' => [
                        'name' => 'Outer Oval Loop',
                        'derby' => false,
                    ],
                ],
            ],
            'sandstone_raceway' => [
                'name' => 'Sandstone Raceway',
                'weather' => [
                    0 => 'clear',
                ],
                'variants' => [
                    'sandpit1_long_loop' => [
                        'name' => 'Main Route',
                        'derby' => false,
                    ],
                    'sandpit1_long_loop_rev' => [
                        'name' => 'Main Route Reverse',
                        'derby' => false,
                    ],
                    'sandpit1_short_loop' => [
                        'name' => 'Short Route',
                        'derby' => false,
                    ],
                    'sandpit1_short_loop_rev' => [
                        'name' => 'Short Route Reverse',
                        'derby' => false,
                    ],
                    'sandpit1_alt_loop' => [
                        'name' => 'Alt Route',
                        'derby' => false,
                    ],
                    'sandpit1_alt_loop_rev' => [
                        'name' => 'Alt Route Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'savolax_sandpit' => [
                'name' => 'Savolax Sandpit',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'sandpit2_full_circuit' => [
                        'name' => 'Main Route',
                        'derby' => false,
                    ],
                    'sandpit2_full_circuit_rev' => [
                        'name' => 'Main Route Reverse',
                        'derby' => false,
                    ],
                    'sandpit2_2' => [
                        'name' => 'Short Route',
                        'derby' => false,
                    ],
                    'sandpit2_2_rev' => [
                        'name' => 'Short Route Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'boulder_bank_circuit' => [
                'name' => 'Boulder Bank Circuit',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                    2 => 'fog',
                ],
                'variants' => [
                    'sandpit3_long_loop' => [
                        'name' => 'Main Route',
                        'derby' => false,
                    ],
                    'sandpit3_long_loop_rev' => [
                        'name' => 'Main Route Reverse',
                        'derby' => false,
                    ],
                    'sandpit3_short_loop' => [
                        'name' => 'Short Route',
                        'derby' => false,
                    ],
                    'sandpit3_short_loop_rev' => [
                        'name' => 'Short Route Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'fire_rock_raceway' => [
                'name' => 'Fire Rock Raceway',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'tarmac1_main_circuit' => [
                        'name' => 'Full Circuit',
                        'derby' => false,
                    ],
                    'tarmac1_main_circuit_rev' => [
                        'name' => 'Full Circuit Reverse',
                        'derby' => false,
                    ],
                    'tarmac1_short_circuit' => [
                        'name' => 'Short Circuit',
                        'derby' => false,
                    ],
                    'tarmac1_short_circuit_rev' => [
                        'name' => 'Short Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'motorcity_circuit' => [
                'name' => 'Motorcity Circuit',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'tarmac2_main_circuit' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'tarmac2_main_circuit_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                    'tarmac2_main_circuit_tourney' => [
                        'name' => 'Trophy Circuit',
                        'derby' => false,
                    ],
                ],
            ],
            'espedalen_raceway' => [
                'name' => 'Espedalen Raceway',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'tarmac3_main_circuit' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'tarmac3_main_circuit_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                    'tarmac3_short_circuit' => [
                        'name' => 'Short Circuit',
                        'derby' => false,
                    ],
                    'tarmac3_short_circuit_rev' => [
                        'name' => 'Short Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'finncross_circuit' => [
                'name' => 'Finncross Circuit',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'mixed1_main_circuit' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'mixed1_main_circuit_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'maasten_motocenter' => [
                'name' => 'Maasten Motocenter',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'mixed2_main_circuit' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'mixed2_main_circuit_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'pinehills_raceway' => [
                'name' => 'Pinehills Raceway',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'mixed3_long_loop' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'mixed3_long_loop_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                    'mixed3_r3' => [
                        'name' => 'Rally Circuit',
                        'derby' => false,
                    ],
                    'mixed3_r3_rev' => [
                        'name' => 'Rally Circuit Reverse',
                        'derby' => false,
                    ],
                    'mixed3_short_loop' => [
                        'name' => 'Short Circuit',
                        'derby' => false,
                    ],
                    'mixed3_short_loop_rev' => [
                        'name' => 'Short Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'rosenheim_raceway' => [
                'name' => 'Rosenheim Raceway',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'mixed4_main_circuit' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'mixed4_main_circuit_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'northland_raceway' => [
                'name' => 'Northland Raceway',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'mixed5_outer_loop' => [
                        'name' => 'Outer Route',
                        'derby' => false,
                    ],
                    'mixed5_outer_loop_rev' => [
                        'name' => 'Outer Route Reverse',
                        'derby' => false,
                    ],
                    'mixed5_inner_loop' => [
                        'name' => 'Inner Route',
                        'derby' => false,
                    ],
                    'mixed5_inner_loop_rev' => [
                        'name' => 'Inner Route Reverse',
                        'derby' => false,
                    ],
                    'mixed5_free_route' => [
                        'name' => 'Free Route',
                        'derby' => false,
                    ],
                ],
            ],
            'firwood_motocenter' => [
                'name' => 'Firwood Motocenter',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                    2 => 'fog',
                ],
                'variants' => [
                    'mixed7_r1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'mixed7_r1_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                    'mixed7_r2' => [
                        'name' => 'Rally Circuit',
                        'derby' => false,
                    ],
                    'mixed7_r2_rev' => [
                        'name' => 'Rally Circuit Reverse',
                        'derby' => false,
                    ],
                    'mixed7_r3' => [
                        'name' => 'Short Circuit',
                        'derby' => false,
                    ],
                    'mixed7_r3_rev' => [
                        'name' => 'Short Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'northfolk_ring' => [
                'name' => 'Northfolk Ring',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                    2 => 'fog',
                ],
                'variants' => [
                    'mixed8_r1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'mixed8_r2' => [
                        'name' => 'Open Circuit',
                        'derby' => false,
                    ],
                    'mixed8_r3_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'clayridge_circuit' => [
                'name' => 'Clayridge Circuit',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'mixed9_r1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'mixed9_r1_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'deathloop' => [
                'name' => 'Deathloop',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'loop' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                ],
            ],
            'dirt_devil_stadium' => [
                'name' => 'Dirt Devil Stadium',
                'weather' => [
                    0 => 'clear',
                    1 => 'overcast',
                ],
                'variants' => [
                    'triangle_r1' => [
                        'name' => 'Dirt Speedway',
                        'derby' => false,
                    ],
                    'triangle_r2' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'bleak_city' => [
                'name' => 'Bleak City',
                'weather' => null,
                'variants' => [
                    'crm01_1' => [
                        'name' => 'Race Track',
                        'derby' => false,
                    ],
                    'crm01_2' => [
                        'name' => 'Free Roam',
                        'derby' => true,
                    ],
                    'crm01_3' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                    'crm01_5' => [
                        'name' => 'Race Track Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'devils_canyon' => [
                'name' => 'Devil\'s Canyon',
                'weather' => null,
                'variants' => [
                    'crm02_1' => [
                        'name' => 'Race Track',
                        'derby' => false,
                    ],
                    'crm02_2' => [
                        'name' => 'Free Roam',
                        'derby' => true,
                    ],
                ],
            ],
            'drytown_desert_circuit' => [
                'name' => 'Drytown Desert Circuit',
                'weather' => null,
                'variants' => [
                    'fields08_1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'fields08_1_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'rockfield_roughspot' => [
                'name' => 'Rockfield Roughspot',
                'weather' => null,
                'variants' => [
                    'fields09_1' => [
                        'name' => 'Dirt Oval',
                        'derby' => false,
                    ],
                ],
            ],
            'mudford_motorpark' => [
                'name' => 'Mudford Motorpark',
                'weather' => null,
                'variants' => [
                    'fields10_1' => [
                        'name' => 'Mud Oval',
                        'derby' => false,
                    ],
                    'fields10_2' => [
                        'name' => 'Mud Pit',
                        'derby' => true,
                    ],
                ],
            ],
            'the_maw' => [
                'name' => 'The Maw',
                'weather' => null,
                'variants' => [
                    'fields11_1' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'kingston_raceway' => [
                'name' => 'Kingston Raceway',
                'weather' => null,
                'variants' => [
                    'fields12_1' => [
                        'name' => 'Asphalt Oval',
                        'derby' => false,
                    ],
                    'fields12_1_rev' => [
                        'name' => 'Asphalt Oval Reverse',
                        'derby' => false,
                    ],
                    'fields12_2' => [
                        'name' => 'Figure 8',
                        'derby' => false,
                    ],
                ],
            ],
            'eagles_peak_motorpark' => [
                'name' => 'Eagles Peak Motorpark',
                'weather' => null,
                'variants' => [
                    'fields13_1' => [
                        'name' => 'Racing Track',
                        'derby' => false,
                    ],
                    'fields13_1_rev' => [
                        'name' => 'Racing Track Reverse',
                        'derby' => false,
                    ],
                    'fields13_2' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'rattlesnake_racepark' => [
                'name' => 'Rattlesnake Racepark',
                'weather' => null,
                'variants' => [
                    'fields14_1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'fields14_2' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'vale_falls_circuit' => [
                'name' => 'Vale Falls Circuit',
                'weather' => null,
                'variants' => [
                    'forest11_1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'forest11_1_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                    'forest11_2' => [
                        'name' => 'Short Circuit',
                        'derby' => false,
                    ],
                    'forest11_2_rev' => [
                        'name' => 'Short Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'tribend_speedway' => [
                'name' => 'Tribend Speedway',
                'weather' => null,
                'variants' => [
                    'forest12_1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'forest12_1_rev' => [
                        'name' => 'Reverse Circuit',
                        'derby' => false,
                    ],
                    'forest12_2' => [
                        'name' => 'Wild Circuit',
                        'derby' => false,
                    ],
                ],
            ],
            'torsdalen_circuit' => [
                'name' => 'Torsdalen Circuit',
                'weather' => null,
                'variants' => [
                    'forest13_1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'forest13_1_rev' => [
                        'name' => 'Main Circuit Reverse',
                        'derby' => false,
                    ],
                    'forest13_2' => [
                        'name' => 'Short Circuit',
                        'derby' => false,
                    ],
                    'forest13_2_rev' => [
                        'name' => 'Short Circuit Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'rally_trophy' => [
                'name' => 'Rally Trophy',
                'weather' => null,
                'variants' => [
                    'rt01_1' => [
                        'name' => 'Special Stage',
                        'derby' => false,
                    ],
                ],
            ],
            'hellride' => [
                'name' => 'Hellride',
                'weather' => null,
                'variants' => [
                    'urban06' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                ],
            ],
            'thunderbowl' => [
                'name' => 'Thunderbowl',
                'weather' => null,
                'variants' => [
                    'urban07' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'hillstreet_circuit' => [
                'name' => 'Hillstreet Circuit',
                'weather' => null,
                'variants' => [
                    'urban08_1' => [
                        'name' => 'Race Track',
                        'derby' => false,
                    ],
                    'urban08_1_rev' => [
                        'name' => 'Race Track Reverse',
                        'derby' => false,
                    ],
                ],
            ],
            'wrecknado' => [
                'name' => 'Wrecknado',
                'weather' => null,
                'variants' => [
                    'urban09_1' => [
                        'name' => 'Main Circuit',
                        'derby' => false,
                    ],
                    'urban09_2' => [
                        'name' => 'Demolition Arena',
                        'derby' => true,
                    ],
                ],
            ],
            'wrecking_playground' => [
                'name' => 'Wrecking Playground',
                'weather' => null,
                'variants' => [
                    'wrecker01_1' => [
                        'name' => 'Main Area',
                        'derby' => true,
                    ],
                ],
            ],
        ];
    }
}
