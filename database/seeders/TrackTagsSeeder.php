<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\TrackVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrackTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create all tags
        $tags = $this->createTags();

        // Then assign tags to track variants
        $this->assignTagsToTracks($tags);
    }

    private function createTags(): array
    {
        $tagDefinitions = [
            // Track Layout Tags
            ['name' => 'Oval', 'slug' => 'oval', 'color' => '#FF6B6B'],
            ['name' => 'Figure 8', 'slug' => 'figure-8', 'color' => '#4ECDC4'],
            ['name' => 'Circuit', 'slug' => 'circuit', 'color' => '#45B7D1'],
            ['name' => 'Speedway', 'slug' => 'speedway', 'color' => '#FFA07A'],
            ['name' => 'Wild', 'slug' => 'wild', 'color' => '#98D8C8'],

            // Surface Type Tags
            ['name' => 'Gravel', 'slug' => 'gravel', 'color' => '#A0826D'],
            ['name' => 'Tarmac', 'slug' => 'tarmac', 'color' => '#2C3E50'],
            ['name' => 'Mud', 'slug' => 'mud', 'color' => '#6B4423'],

            // Specialty Tags
            ['name' => 'Rally', 'slug' => 'rally', 'color' => '#E74C3C'],
            ['name' => 'Stadium', 'slug' => 'stadium', 'color' => '#9B59B6'],
            ['name' => 'Urban', 'slug' => 'urban', 'color' => '#34495E'],
            ['name' => 'Forest', 'slug' => 'forest', 'color' => '#27AE60'],
            ['name' => 'Bump', 'slug' => 'bump', 'color' => '#F39C12'],
            ['name' => 'Jump', 'slug' => 'jump', 'color' => '#E67E22'],
            ['name' => 'Loop', 'slug' => 'loop', 'color' => '#1ABC9C'],
            ['name' => 'Two-way Traffic', 'slug' => 'two-way-traffic', 'color' => '#E74C3C'],
            ['name' => 'Intersection', 'slug' => 'intersection', 'color' => '#F1C40F'],
            ['name' => 'Split Path', 'slug' => 'split-path', 'color' => '#3498DB'],
            ['name' => 'Reversed', 'slug' => 'reversed', 'color' => '#95A5A6'],
            ['name' => 'Short', 'slug' => 'short', 'color' => '#7F8C8D'],
            ['name' => 'Wall Ride', 'slug' => 'wallride', 'color' => '#16A085'],
        ];

        $tagMap = [];
        foreach ($tagDefinitions as $tagDef) {
            $tag = Tag::firstOrCreate(
                ['slug' => $tagDef['slug']],
                ['name' => $tagDef['name'], 'color' => $tagDef['color']]
            );
            $tagMap[$tagDef['slug']] = $tag->id;
        }

        return $tagMap;
    }

    private function assignTagsToTracks(array $tags): void
    {
        // Map of variant_id => tag slugs from the proposal
        $trackTagAssignments = [
            // Big Valley Speedway
            'speedway2_demolition_arena' => ['tarmac', 'stadium'],
            'speedway2_figure_8' => ['figure-8', 'tarmac', 'stadium', 'intersection'],
            'speedway2_inner_oval' => ['oval', 'tarmac', 'stadium'],
            'speedway2_classic_arena' => ['tarmac', 'stadium'],
            'speedway2_outer_oval' => ['oval', 'tarmac', 'stadium'],
            'speedway2_oval_loop' => ['oval', 'tarmac', 'two-way-traffic', 'stadium'],

            // Bleak City
            'crm01_3' => ['urban', 'gravel'],
            'crm01_2' => ['urban', 'gravel'],
            'crm01_1' => ['urban', 'tarmac'],
            'crm01_5' => ['urban', 'tarmac', 'reversed'],

            // Bloomfield Speedway
            'dirt_speedway_dirt_oval' => ['oval', 'gravel'],
            'dirt_speedway_figure_8' => ['figure-8', 'gravel', 'intersection'],

            // Bonebreaker Valley
            'bonebreaker_valley_main_circuit' => ['gravel', 'jump', 'two-way-traffic', 'forest'],

            // Boulder Bank Circuit
            'sandpit3_long_loop' => ['tarmac', 'gravel', 'intersection', 'forest'],
            'sandpit3_long_loop_rev' => ['tarmac', 'gravel', 'intersection', 'reversed', 'forest'],
            'sandpit3_short_loop' => ['tarmac', 'gravel', 'intersection', 'forest'],
            'sandpit3_short_loop_rev' => ['tarmac', 'gravel', 'intersection', 'reversed', 'forest'],

            // Clayridge Circuit
            'mixed9_r1' => ['tarmac', 'gravel', 'split-path', 'forest'],
            'mixed9_r1_rev' => ['tarmac', 'gravel', 'split-path', 'reversed', 'forest'],

            // Crash Canyon
            'crash_canyon_main_circuit' => ['gravel', 'two-way-traffic', 'bump'],

            // Deathloop
            'loop' => ['tarmac', 'loop', 'split-path', 'intersection', 'jump', 'bump'],

            // Devil's Canyon
            'crm02_2' => ['tarmac', 'gravel'],
            'crm02_1' => ['tarmac', 'gravel'],

            // Dirt Devil Stadium
            'triangle_r2' => ['stadium', 'gravel', 'bump'],
            'triangle_r1' => ['stadium', 'gravel', 'bump'],

            // Drytown Desert Circuit
            'fields08_1' => ['gravel', 'intersection'],
            'fields08_1_rev' => ['gravel', 'intersection', 'reversed'],

            // Eagles Peak Motorpark
            'fields13_2' => ['tarmac', 'gravel'],
            'fields13_1' => ['tarmac', 'gravel', 'jump', 'intersection'],
            'fields13_1_rev' => ['tarmac', 'gravel', 'jump', 'intersection', 'reversed'],

            // Espedalen Raceway
            'tarmac3_main_circuit' => ['tarmac'],
            'tarmac3_main_circuit_rev' => ['tarmac'],
            'tarmac3_short_circuit' => ['tarmac', 'short'],
            'tarmac3_short_circuit_rev' => ['tarmac', 'short'],

            // Fairfield County
            'smallstadium_demolition_arena' => ['stadium', 'tarmac'],

            // Fairfield Grass Field
            'grass_arena_demolition_arena' => ['gravel'],

            // Fairfield Mud Pit
            'mudpit_demolition_arena' => ['mud'],

            // Finncross Circuit
            'mixed1_main_circuit' => ['tarmac', 'gravel', 'forest'],
            'mixed1_main_circuit_rev' => ['tarmac', 'gravel', 'reversed', 'forest'],

            // Fire Rock Raceway
            'tarmac1_main_circuit' => ['tarmac', 'gravel'],
            'tarmac1_main_circuit_rev' => ['tarmac', 'gravel', 'reversed'],
            'tarmac1_short_circuit' => ['tarmac', 'gravel', 'short'],
            'tarmac1_short_circuit_rev' => ['tarmac', 'gravel', 'short', 'reversed'],

            // Firwood Motocenter
            'mixed7_r1' => ['tarmac', 'gravel'],
            'mixed7_r1_rev' => ['tarmac', 'gravel', 'reversed'],
            'mixed7_r2' => ['tarmac', 'gravel'],
            'mixed7_r2_rev' => ['tarmac', 'gravel', 'reversed'],
            'mixed7_r3' => ['tarmac', 'gravel', 'reversed', 'short'],
            'mixed7_r3_rev' => ['tarmac', 'gravel', 'reversed', 'short'],

            // Glendale Countryside
            'field_derby_arena' => ['gravel'],

            // Hellride
            'urban06' => ['stadium', 'tarmac', 'jump', 'wallride'],

            // Hillstreet Circuit
            'urban08_1' => ['urban', 'tarmac', 'intersection'],
            'urban08_1_rev' => ['urban', 'tarmac', 'intersection', 'reversed'],

            // Hilltop Stadium
            'speedway1_figure_8' => ['figure-8', 'stadium', 'tarmac', 'intersection'],
            'speedway1_oval' => ['oval', 'stadium', 'tarmac'],

            // Kingston Raceway
            'fields12_1' => ['oval', 'tarmac'],
            'fields12_1_rev' => ['oval', 'tarmac', 'reversed'],
            'fields12_2' => ['figure-8', 'gravel', 'intersection'],

            // Maasten Motocenter
            'mixed2_main_circuit' => ['gravel', 'tarmac'],
            'mixed2_main_circuit_rev' => ['gravel', 'tarmac', 'reversed'],

            // Madman Stadium
            'bigstadium_demolition_arena' => ['stadium', 'tarmac', 'wallride'],
            'bigstadium_figure_8' => ['figure-8', 'stadium', 'tarmac', 'intersection', 'bump'],

            // Midwest Motocenter
            'gravel1_main_loop' => ['gravel'],
            'gravel1_main_loop_rev' => ['gravel', 'reversed'],

            // Motorcity Circuit
            'tarmac2_main_circuit' => ['tarmac'],
            'tarmac2_main_circuit_rev' => ['tarmac', 'reversed'],
            'tarmac2_main_circuit_tourney' => ['tarmac', 'jump', 'wild'],

            // Mudford Motorpark
            'fields10_1' => ['oval', 'mud'],
            'fields10_2' => ['mud'],

            // Northfolk Ring
            'mixed8_r1' => ['tarmac', 'gravel'],
            'mixed8_r3_rev' => ['tarmac', 'gravel', 'reversed'],
            'mixed8_r2' => ['tarmac', 'gravel'],

            // Northland Raceway
            'mixed5_free_route' => ['tarmac', 'gravel', 'split-path'],
            'mixed5_inner_loop' => ['tarmac', 'gravel'],
            'mixed5_inner_loop_rev' => ['tarmac', 'gravel', 'reversed'],
            'mixed5_outer_loop' => ['tarmac', 'gravel'],
            'mixed5_outer_loop_rev' => ['tarmac', 'gravel', 'reversed'],

            // Pinehills Raceway
            'mixed3_long_loop' => ['tarmac', 'gravel', 'forest'],
            'mixed3_long_loop_rev' => ['tarmac', 'gravel', 'reversed', 'forest'],
            'mixed3_r3' => ['tarmac', 'gravel', 'forest'],
            'mixed3_r3_rev' => ['tarmac', 'gravel', 'reversed', 'forest'],
            'mixed3_short_loop' => ['tarmac', 'gravel', 'short', 'forest'],
            'mixed3_short_loop_rev' => ['tarmac', 'gravel', 'reversed', 'short', 'forest'],

            // Rally Trophy
            'rt01_1' => ['gravel', 'forest'],

            // Rattlesnake Racepark
            'fields14_1' => ['gravel', 'jump', 'bump'],
            'fields14_2' => ['gravel', 'jump', 'reversed', 'bump'],

            // Rockfield Roughspot
            'fields09_1' => ['oval', 'gravel', 'bump', 'wallride'],

            // Rosenheim Raceway
            'mixed4_main_circuit' => ['tarmac', 'gravel', 'split-path'],
            'mixed4_main_circuit_rev' => ['tarmac', 'gravel', 'reversed', 'split-path'],

            // Sandstone Raceway
            'sandpit1_alt_loop' => ['gravel'],
            'sandpit1_alt_loop_rev' => ['gravel', 'reversed'],
            'sandpit1_long_loop' => ['gravel', 'short'],
            'sandpit1_long_loop_rev' => ['gravel', 'reversed', 'short'],
            'sandpit1_short_loop' => ['gravel', 'intersection', 'two-way-traffic'],
            'sandpit1_short_loop_rev' => ['gravel', 'intersection', 'two-way-traffic', 'reversed'],

            // Savolax Sandpit
            'sandpit2_full_circuit' => ['gravel', 'tarmac'],
            'sandpit2_full_circuit_rev' => ['gravel', 'tarmac', 'reversed'],
            'sandpit2_2' => ['gravel', 'tarmac', 'short'],
            'sandpit2_2_rev' => ['gravel', 'tarmac', 'reversed', 'short'],

            // The Maw
            'fields11_1' => ['tarmac'],

            // Thunderbowl
            'urban07' => ['stadium', 'wallride'],

            // Torsdalen Circuit
            'forest13_1' => ['gravel', 'tarmac'],
            'forest13_1_rev' => ['gravel', 'tarmac', 'reversed'],
            'forest13_2' => ['gravel', 'tarmac', 'short'],
            'forest13_2_rev' => ['gravel', 'tarmac', 'short', 'reversed'],

            // Tribend Speedway
            'forest12_1' => ['tarmac', 'stadium'],
            'forest12_1_rev' => ['tarmac', 'reversed', 'stadium'],
            'forest12_2' => ['tarmac', 'jump', 'wild', 'stadium'],

            // Vale Falls Circuit
            'forest11_1' => ['gravel', 'tarmac'],
            'forest11_1_rev' => ['gravel', 'tarmac', 'reversed'],
            'forest11_2' => ['gravel', 'tarmac', 'short'],
            'forest11_2_rev' => ['gravel', 'tarmac', 'short', 'reversed'],

            // Wrecking Playground
            'wrecker01_1' => ['tarmac', 'gravel', 'jump'],

            // Wrecknado
            'urban09_2' => ['stadium', 'tarmac'],
            'urban09_1' => ['stadium', 'tarmac', 'jump', 'intersection'],
        ];

        foreach ($trackTagAssignments as $variantId => $tagSlugs) {
            $variant = TrackVariant::where('variant_id', $variantId)->first();

            if (!$variant) {
                $this->command->warn("Track variant not found: {$variantId}");
                continue;
            }

            $tagIds = [];
            foreach ($tagSlugs as $slug) {
                if (isset($tags[$slug])) {
                    $tagIds[] = $tags[$slug];
                } else {
                    $this->command->warn("Tag not found: {$slug} for variant {$variantId}");
                }
            }

            if (!empty($tagIds)) {
                $variant->tags()->sync($tagIds);
                $this->command->info("Tagged {$variant->name} with " . count($tagIds) . " tags");
            }
        }

        $this->command->info("\nSuccessfully seeded " . count($tags) . " tags and tagged " . count($trackTagAssignments) . " track variants!");
    }
}
