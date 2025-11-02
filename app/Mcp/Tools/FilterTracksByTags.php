<?php

namespace App\Mcp\Tools;

use App\Models\TrackVariant;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class FilterTracksByTags extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Filter tracks by one or more tags. Provide tag names (e.g., 'Oval', 'Tarmac', 'Stadium') to find tracks that match ALL specified tags (AND logic). Returns track IDs, names, and their complete tag lists.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $tagNames = $request->get('tags', []);
        $matchAll = $request->get('match_all', true);

        if (empty($tagNames)) {
            return Response::text("Error: No tags specified. Please provide at least one tag name.");
        }

        // Ensure tags is an array
        if (!is_array($tagNames)) {
            $tagNames = [$tagNames];
        }

        // Build the query
        $query = TrackVariant::with(['track', 'tags']);

        if ($matchAll) {
            // Match ALL tags (AND logic)
            foreach ($tagNames as $tagName) {
                $query->whereHas('tags', function ($q) use ($tagName) {
                    $normalizedTag = $this->normalizeTagName($tagName);
                    $q->where('name', 'LIKE', "%{$normalizedTag}%")
                      ->orWhere('slug', 'LIKE', "%{$normalizedTag}%");
                });
            }
        } else {
            // Match ANY tags (OR logic)
            $query->whereHas('tags', function ($q) use ($tagNames) {
                $q->where(function($subQuery) use ($tagNames) {
                    foreach ($tagNames as $tagName) {
                        $normalizedTag = $this->normalizeTagName($tagName);
                        $subQuery->orWhere('name', 'LIKE', "%{$normalizedTag}%")
                                ->orWhere('slug', 'LIKE', "%{$normalizedTag}%");
                    }
                });
            });
        }

        $tracks = $query->get();

        if ($tracks->isEmpty()) {
            $tagsList = implode(', ', $tagNames);
            $logic = $matchAll ? 'ALL' : 'ANY';
            return Response::text("No tracks found matching {$logic} of the tags: {$tagsList}");
        }

        $tagsList = implode(', ', $tagNames);
        $logic = $matchAll ? 'ALL' : 'ANY';
        $output = "Found {$tracks->count()} tracks matching {$logic} tags ({$tagsList}):\n\n";

        // Group by type
        $racingTracks = $tracks->filter(fn($t) => strtolower($t->game_mode) === 'racing');
        $derbyTracks = $tracks->filter(fn($t) => strtolower($t->game_mode) === 'derby');

        if ($racingTracks->isNotEmpty()) {
            $output .= "🏁 RACING TRACKS (" . $racingTracks->count() . "):\n";
            foreach ($racingTracks as $track) {
                $output .= "  • {$track->full_name}\n";
                $output .= "    ID: {$track->variant_id}\n";
                $tags = $track->tags->pluck('name')->toArray();
                if (!empty($tags)) {
                    $output .= "    Tags: " . implode(', ', $tags) . "\n";
                }
            }
            $output .= "\n";
        }

        if ($derbyTracks->isNotEmpty()) {
            $output .= "💥 DERBY TRACKS (" . $derbyTracks->count() . "):\n";
            foreach ($derbyTracks as $track) {
                $output .= "  • {$track->full_name}\n";
                $output .= "    ID: {$track->variant_id}\n";
                $tags = $track->tags->pluck('name')->toArray();
                if (!empty($tags)) {
                    $output .= "    Tags: " . implode(', ', $tags) . "\n";
                }
            }
        }

        return Response::text($output);
    }

    /**
     * Normalize tag name for flexible matching
     * Handles plurals, case, and common variations
     */
    protected function normalizeTagName(string $tagName): string
    {
        // Convert to lowercase for case-insensitive matching
        $normalized = strtolower(trim($tagName));

        // Remove common plural suffixes to match singular forms
        // "bumps" -> "bump", "jumps" -> "jump", etc.
        $normalized = preg_replace('/s$/', '', $normalized);

        // Handle "es" plurals: "intersections" -> "intersection"
        $normalized = preg_replace('/es$/', '', $normalized);

        return $normalized;
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'tags' => $schema->array()
                ->items($schema->string())
                ->description('Array of tag names to filter by (e.g., ["Oval", "Tarmac"]). Case-insensitive and handles plurals (e.g., "bumps" matches "Bump").'),
            'match_all' => $schema->boolean()
                ->description('If true (default), tracks must have ALL specified tags. If false, tracks with ANY of the tags will be returned.'),
        ];
    }
}
