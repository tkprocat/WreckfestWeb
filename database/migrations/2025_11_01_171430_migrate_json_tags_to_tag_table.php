<?php

use App\Models\Tag;
use App\Models\TrackVariant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all unique tags from track_variants.tags JSON field
        $allTags = [];

        TrackVariant::all()->each(function ($variant) use (&$allTags) {
            $tags = $variant->tags ?? [];
            foreach ($tags as $tag) {
                if (!empty($tag) && is_string($tag)) {
                    $allTags[] = $tag;
                }
            }
        });

        // Get unique tags
        $uniqueTags = array_unique($allTags);

        // Create Tag records
        $tagMap = [];
        foreach ($uniqueTags as $tagName) {
            $slug = Str::slug($tagName);

            $tag = Tag::firstOrCreate(
                ['slug' => $slug],
                ['name' => $tagName]
            );

            $tagMap[$tagName] = $tag->id;
        }

        // Now create pivot table entries
        TrackVariant::all()->each(function ($variant) use ($tagMap) {
            $tags = $variant->tags ?? [];
            $tagIds = [];

            foreach ($tags as $tagName) {
                if (!empty($tagName) && is_string($tagName) && isset($tagMap[$tagName])) {
                    $tagIds[] = $tagMap[$tagName];
                }
            }

            if (!empty($tagIds)) {
                $variant->tags()->sync($tagIds);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all pivot table entries
        \DB::table('track_variant_tag')->truncate();

        // Remove all tags
        Tag::truncate();
    }
};
