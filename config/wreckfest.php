<?php

return [
    'api_url' => env('WRECKFEST_API_URL', 'https://localhost:5101/api'),
    'ws_url' => env('WRECKFEST_WS_URL', 'wss://localhost:5101/ws'),
    'ocr_enabled' => env('WRECKFEST_OCR_ENABLED', false),

    // Brand colors used across the application (both frontend and admin panel)
    'brand' => [
        'primary' => '#a03d00',
    ],

    'tracks' => [
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
    ],

    'gamemodes' => [
        'racing' => 'Racing',
        'derby' => 'Last Man Standing',
        'derby deathmatch' => 'Deathmatch',
        'team derby' => 'Team Deathmatch',
        'team race' => 'Team Race',
        'elimination race' => 'Elimination Race',
    ],

    // Gamemode categories for track compatibility
    'derby_gamemodes' => [
        'derby',
        'derby deathmatch',
        'team derby',
    ],

    'racing_gamemodes' => [
        'racing',
        'team race',
        'elimination race',
    ],

    // Tracks that are only compatible with derby gamemodes (demolition arenas)
    'derby_only_tracks' => [
        'bigstadium_demolition_arena',
        'crm01_3',
        'field_derby_arena',
        'fields11_1',
        'fields13_2',
        'grass_arena_demolition_arena',
        'mudpit_demolition_arena',
        'smallstadium_demolition_arena',
        'speedway2_classic_arena',
        'speedway2_demolition_arena',
        'triangle_r2',
        'urban07',
        'urban09_2',
    ],

    /*
     * Track weather compatibility
     * Maps track IDs to their supported weather conditions
     * Tracks not listed here support all weather types
     * Based on: https://tads.me.uk/wfwiki/index.php?title=Servers:Track_Names
     */
    'track_weather_compatibility' => [
        // Madman Stadium - clear, overcast only
        'bigstadium_demolition_arena' => ['clear', 'overcast'],
        'bigstadium_figure_8' => ['clear', 'overcast'],

        // Fairfield venues - clear, overcast, fog
        'smallstadium_demolition_arena' => ['clear', 'overcast', 'fog'],
        'mudpit_demolition_arena' => ['clear', 'overcast', 'fog'],
        'grass_arena_demolition_arena' => ['clear', 'overcast', 'fog'],

        // Glendale Countryside - overcast only
        'field_derby_arena' => ['overcast'],

        // Bloomfield Speedway - clear, overcast
        'dirt_speedway_dirt_oval' => ['clear', 'overcast'],
        'dirt_speedway_figure_8' => ['clear', 'overcast'],

        // Bonebreaker Valley - clear, overcast
        'bonebreaker_valley_main_circuit' => ['clear', 'overcast'],

        // Crash Canyon - clear, overcast
        'crash_canyon_main_circuit' => ['clear', 'overcast'],

        // Midwest Motocenter - overcast only
        'gravel1_main_loop' => ['overcast'],
        'gravel1_main_loop_rev' => ['overcast'],

        // Hilltop Stadium - clear, overcast, fog
        'speedway1_figure_8' => ['clear', 'overcast', 'fog'],
        'speedway1_oval' => ['clear', 'overcast', 'fog'],

        // Big Valley Speedway - clear, overcast
        'speedway2_classic_arena' => ['clear', 'overcast'],
        'speedway2_demolition_arena' => ['clear', 'overcast'],
        'speedway2_figure_8' => ['clear', 'overcast'],
        'speedway2_inner_oval' => ['clear', 'overcast'],
        'speedway2_outer_oval' => ['clear', 'overcast'],
        'speedway2_oval_loop' => ['clear', 'overcast'],

        // Sandstone Raceway - clear only
        'sandpit1_long_loop' => ['clear'],
        'sandpit1_long_loop_rev' => ['clear'],
        'sandpit1_short_loop' => ['clear'],
        'sandpit1_short_loop_rev' => ['clear'],
        'sandpit1_alt_loop' => ['clear'],
        'sandpit1_alt_loop_rev' => ['clear'],

        // Savolax Sandpit - clear, overcast
        'sandpit2_2' => ['clear', 'overcast'],
        'sandpit2_2_rev' => ['clear', 'overcast'],
        'sandpit2_full_circuit' => ['clear', 'overcast'],
        'sandpit2_full_circuit_rev' => ['clear', 'overcast'],

        // Boulder Bank Circuit - clear, overcast, fog
        'sandpit3_long_loop' => ['clear', 'overcast', 'fog'],
        'sandpit3_long_loop_rev' => ['clear', 'overcast', 'fog'],
        'sandpit3_short_loop' => ['clear', 'overcast', 'fog'],
        'sandpit3_short_loop_rev' => ['clear', 'overcast', 'fog'],

        // Fire Rock Raceway - clear, overcast
        'tarmac1_main_circuit' => ['clear', 'overcast'],
        'tarmac1_main_circuit_rev' => ['clear', 'overcast'],
        'tarmac1_short_circuit' => ['clear', 'overcast'],
        'tarmac1_short_circuit_rev' => ['clear', 'overcast'],

        // Motorcity Circuit - clear, overcast
        'tarmac2_main_circuit' => ['clear', 'overcast'],
        'tarmac2_main_circuit_rev' => ['clear', 'overcast'],

        // Espedalen Raceway - clear, overcast
        'tarmac3_main_circuit' => ['clear', 'overcast'],
        'tarmac3_main_circuit_rev' => ['clear', 'overcast'],
        'tarmac3_short_circuit' => ['clear', 'overcast'],
        'tarmac3_short_circuit_rev' => ['clear', 'overcast'],

        // Finncross Circuit - clear, overcast
        'mixed1_main_circuit' => ['clear', 'overcast'],
        'mixed1_main_circuit_rev' => ['clear', 'overcast'],

        // Maasten Motorcenter - clear, overcast
        'mixed2_main_circuit' => ['clear', 'overcast'],
        'mixed2_main_circuit_rev' => ['clear', 'overcast'],

        // Pinehills Raceway - clear, overcast
        'mixed3_long_loop' => ['clear', 'overcast'],
        'mixed3_long_loop_rev' => ['clear', 'overcast'],
        'mixed3_short_loop' => ['clear', 'overcast'],
        'mixed3_short_loop_rev' => ['clear', 'overcast'],

        // Rosenheim Raceway - clear, overcast
        'mixed4_main_circuit' => ['clear', 'overcast'],
        'mixed4_main_circuit_rev' => ['clear', 'overcast'],

        // Northland Raceway - clear, overcast
        'mixed5_outer_loop' => ['clear', 'overcast'],
        'mixed5_outer_loop_rev' => ['clear', 'overcast'],
        'mixed5_inner_loop' => ['clear', 'overcast'],
        'mixed5_inner_loop_rev' => ['clear', 'overcast'],
        'mixed5_free_route' => ['clear', 'overcast'],

        // Firwood Motorcenter - clear, overcast, fog
        'mixed7_r1' => ['clear', 'overcast', 'fog'],
        'mixed7_r1_rev' => ['clear', 'overcast', 'fog'],
        'mixed7_r3' => ['clear', 'overcast', 'fog'],
        'mixed7_r3_rev' => ['clear', 'overcast', 'fog'],

        // Clayridge Circuit - clear, overcast
        'mixed9_r1' => ['clear', 'overcast'],
        'mixed9_r1_rev' => ['clear', 'overcast'],

        // Death Loop - clear, overcast
        'loop' => ['clear', 'overcast'],

        // Dirt Devil Stadium - clear, overcast
        'triangle_r1' => ['clear', 'overcast'],
        'triangle_r2' => ['clear', 'overcast'],

        // Northfolk Ring - clear, overcast, fog
        'mixed8_r1' => ['clear', 'overcast', 'fog'],
        'mixed8_r2' => ['clear', 'overcast', 'fog'],
        'mixed8_r3_rev' => ['clear', 'overcast', 'fog'],
    ],

    'grid_orders' => [
        'perf_normal' => 'Performance Normal',
        'perf_reverse' => 'Performance Reverse',
        'random' => 'Random',
        'qualifying' => 'Qualifying',
        'cup_normal' => 'Cup Normal',
        'cup_reverse' => 'Cup Reverse',
    ],

    'session_modes' => [
        'normal' => 'Normal Session (no qualifying, no Cup Points)',
        'qualify-sprint' => 'Qualifying Sprint (race winner gets Pole Position)',
        'qualify-lap' => 'Qualifying Lap (Best Lap gets Pole Position)',
        '30p-aggr' => '30P Aggressive (Winner = 30, 27, 25, 23, 20...)',
        '25p-aggr' => '25P Aggressive (Winner = 25, 18, 15, 12, 10, 8, 6, 4, 2, 1)',
        '25p-mod' => '25P Moderate (Winner = 25, 20, 16, 11...)',
        '24p-lin' => '24P Linear (Winner = 24, then -1 per position)',
        '16p-lin' => '16P Linear (Winner = 16, then -1 per position)',
        '10p-double' => '10P Double (Winner = 20, then -2 per position)',
        '10p-lin' => '10P Linear (Winner = 10, then -1 per position)',
        '35p-folk' => '35P Folk Race (Winner = 35, 30, 25, 20, 18, 16...)',
        'f1-1991' => 'F1 1991 (Winner = 10, 6, 4, 3, 2, 1)',
        'f1-2003' => 'F1 2003 (Winner = 10, 8, 6, 5, 4, 3, 2, 1)',
        'f1-2010' => 'F1 2010 (Winner = 25, 18, 15, 12, 10, 8, 6, 4, 2, 1)',
        'player_count_1' => 'Player Count 1 (Winner = player count, then -1 per position)',
    ],

    'weather_conditions' => [
        'clear' => 'Clear',
        'overcast' => 'Overcast',
        'rain' => 'Rain',
        'storm' => 'Storm',
        'fog' => 'Fog',
        'random' => 'Random',
    ],

    'frequencies' => [
        'low' => 'Low (20 Hz)',
        'high' => 'High (30-60 Hz)',
    ],

    'ai_difficulties' => [
        'novice' => 'Novice',
        'amateur' => 'Amateur',
        'expert' => 'Expert',
    ],

    'vehicle_damages' => [
        'normal' => 'Normal',
        'intense' => 'Intense',
        'realistic' => 'Realistic',
        'extreme' => 'Extreme',
    ],

    'car_classes' => [
        'a' => 'Class A',
        'b' => 'Class B',
        'c' => 'Class C',
        'd' => 'Class D',
        'all' => 'All Classes',
    ],

    'cars' => [
        'bandit' => 'Bandit',
        'bandit ripper v8' => 'Bandit Ripper V8',
        'battle bus' => 'Battle Bus',
        'big rig' => 'Big Rig',
        'blade' => 'Blade',
        'boomer' => 'Boomer',
        'boomer (f)' => 'Boomer (F)',
        'boomer (l)' => 'Boomer (L)',
        'boomer (lr)' => 'Boomer (LR)',
        'boomer rs' => 'Boomer RS',
        'boomer rs (l)' => 'Boomer RS (L)',
        'boomer rs (rt)' => 'Boomer RS (RT)',
        'buggy' => 'Buggy',
        'buggy (f)' => 'Buggy (F)',
        'buggy (l)' => 'Buggy (L)',
        'bugzilla' => 'Bugzilla',
        'bulldog' => 'Bulldog',
        'bullet' => 'Bullet',
        'bumper car' => 'Bumper Car',
        'bumper car (tl)' => 'Bumper Car (TL)',
        'car>dinal' => 'Car>dinal',
        'dominator' => 'Dominator',
        'dominator (b)' => 'Dominator (B)',
        'dominator (l)' => 'Dominator (L)',
        'dominator (tl)' => 'Dominator (TL)',
        'doom rig' => 'Doom Rig',
        'doom rig (tl)' => 'Doom Rig (TL)',
        'double decker' => 'Double Decker',
        'double decker (l)' => 'Double Decker (L)',
        'dragslayer' => 'Dragslayer',
        'dragslayer (l)' => 'Dragslayer (L)',
        'eagle r' => 'Eagle R',
        'el matador' => 'El Matador',
        'el matador (l)' => 'El Matador (L)',
        'firefly' => 'Firefly',
        'firefly (f)' => 'Firefly (F)',
        'firefly (tl)' => 'Firefly (TL)',
        'gatecrasher' => 'Gatecrasher',
        'gorbie' => 'Gorbie',
        'gorbie (l)' => 'Gorbie (L)',
        'grand duke' => 'Grand Duke',
        'gremlin' => 'Gremlin',
        'gremlin (l)' => 'Gremlin (L)',
        'gremlin (tl)' => 'Gremlin (TL)',
        'hammerhead' => 'Hammerhead',
        'hammerhead (f)' => 'Hammerhead (F)',
        'hammerhead (l)' => 'Hammerhead (L)',
        'hammerhead rs' => 'Hammerhead RS',
        'harvester' => 'Harvester',
        'hearse' => 'Hearse',
        'hellvester' => 'Hellvester',
        'honey pot' => 'Honey Pot',
        'hornet' => 'Hornet',
        'hornet (f)' => 'Hornet (F)',
        'hornet (tl)' => 'Hornet (TL)',
        'hotbomb' => 'Hotbomb',
        'hotshot' => 'Hotshot',
        'hotshot (l)' => 'Hotshot (L)',
        'hunter panther' => 'Hunter Panther',
        'killerbee' => 'Killerbee',
        'killerbee (f)' => 'Killerbee (F)',
        'killerbee (l)' => 'Killerbee (L)',
        'killerbee (tl)' => 'Killerbee (TL)',
        'killerbee s' => 'Killerbee S',
        'killerbee s (l)' => 'Killerbee S (L)',
        'killerpig' => 'Killerpig',
        'killerpig (tl)' => 'Killerpig (TL)',
        'lawn mower' => 'Lawn Mower',
        'lawn mower (l)' => 'Lawn Mower (L)',
        'lawn mower (tl)' => 'Lawn Mower (TL)',
        'limo' => 'Limo',
        'limo (l)' => 'Limo (L)',
        'little thrasher' => 'Little Thrasher',
        'motorhome' => 'Motorhome',
        'motorhome (l)' => 'Motorhome (L)',
        'motorhome (tl)' => 'Motorhome (TL)',
        'muddigger' => 'Muddigger',
        'nexus rx' => 'Nexus RX',
        'nexus rx (l)' => 'Nexus RX (L)',
        'nexus rx (rl)' => 'Nexus RX (RL)',
        'outlaw' => 'Outlaw',
        'panther rs' => 'Panther RS',
        'panther rs (b)' => 'Panther RS (B)',
        'pocket rocket' => 'Pocket Rocket',
        'pocket rocket (tl)' => 'Pocket Rocket (TL)',
        'raiden rs' => 'Raiden RS',
        'rammer' => 'Rammer',
        'rammer (l)' => 'Rammer (L)',
        'rammer rs' => 'Rammer RS',
        'rammer rs (l)' => 'Rammer RS (L)',
        'rammer rs (tl)' => 'Rammer RS (TL)',
        'raven' => 'Raven',
        'razor' => 'Razor',
        'rebelrat' => 'Rebelrat',
        'rebelrat (tl)' => 'Rebelrat (TL)',
        'roadcutter' => 'Roadcutter',
        'roadcutter (l)' => 'Roadcutter (L)',
        'roadcutter (tld)' => 'Roadcutter (TLD)',
        'roadslayer' => 'Roadslayer',
        'roadslayer (l)' => 'Roadslayer (L)',
        'roadslayer gt' => 'Roadslayer GT',
        'roadslayer gt (l)' => 'Roadslayer GT (L)',
        'roadslayer gt (tl)' => 'Roadslayer GT (TL)',
        'rocket' => 'Rocket',
        'rocket (l)' => 'Rocket (L)',
        'rocket rx' => 'Rocket RX',
        'sandstorm' => 'Sandstorm',
        'school bus' => 'School Bus',
        'school bus (l)' => 'School Bus (L)',
        'school bus (tl)' => 'School Bus (TL)',
        'sofa car' => 'Sofa Car',
        'sofa car (l)' => 'Sofa Car (L)',
        'sofa car (tl)' => 'Sofa Car (TL)',
        'speedbird' => 'Speedbird',
        'speedbird (l)' => 'Speedbird (L)',
        'speedbird gt' => 'Speedbird GT',
        'speedemon' => 'Speedemon',
        'speedie' => 'Speedie',
        'starbeast' => 'Starbeast',
        'starbeast (l)' => 'Starbeast (L)',
        'starbeast (lr)' => 'Starbeast (LR)',
        'starbeast (tl)' => 'Starbeast (TL)',
        'starbeast ss' => 'Starbeast SS',
        'stellar' => 'Stellar',
        'stellar (l)' => 'Stellar (L)',
        'step van' => 'Step Van',
        'stock car' => 'Stock Car',
        'sunrise super' => 'Sunrise Super',
        'super venom' => 'Super Venom',
        'supervan' => 'Supervan',
        'supervan (l)' => 'Supervan (L)',
        'supervan (tl)' => 'Supervan (TL)',
        'sweeper' => 'Sweeper',
        'sweeper (b)' => 'Sweeper (B)',
        'sweeper (f)' => 'Sweeper (F)',
        'tristar' => 'Tristar',
        'tristar (b)' => 'Tristar (B)',
        'tristar (tl)' => 'Tristar (TL)',
        'trooper' => 'Trooper',
        'trophy runner' => 'Trophy Runner',
        'vandal' => 'Vandal',
        'venom' => 'Venom',
        'wardigger' => 'Wardigger',
        'warwagon' => 'Warwagon',
        'warwagon (dtl)' => 'Warwagon (DTL)',
        'warwagon (l)' => 'Warwagon (L)',
        'warwagon (tl)' => 'Warwagon (TL)',
        'wildking' => 'Wildking',
        'wingman' => 'Wingman',
    ],
];
