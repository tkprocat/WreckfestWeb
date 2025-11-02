<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\TrackMetadata;
use App\Models\TrackVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get track data from config
        $trackLocations = config('wreckfest.tracks', []);

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
                    'weather_conditions' => $weather, // null means all weather supported
                ]);
            }

            $this->command->info("  Added ".count($variants)." variants");
        }

        $totalVariants = TrackVariant::count();
        $this->command->info("Import complete! Created ".Track::count()." tracks with {$totalVariants} variants.");
    }
}
