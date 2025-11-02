<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListTracks extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "List all available tracks with detailed metadata including location, variant name, type (racing/derby), tags, and track categories. Returns comprehensive track information for building collections.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $groupByLocation = $request->get('group_by_location', false);
        $includeMetadata = $request->get('include_metadata', true);

        // Load all track variants with their locations and tags
        $variants = \App\Models\TrackVariant::with(['track', 'tags'])->get();

        $tracks = [];

        foreach ($variants as $variant) {
            $trackInfo = [
                'id' => $variant->variant_id,
                'display_name' => $variant->full_name,
                'location_name' => $variant->track->name,
                'variant_name' => $variant->name,
                'type' => strtolower($variant->game_mode),
            ];

            if ($includeMetadata) {
                $trackInfo['location_key'] = $variant->track->key;
                $trackInfo['weather_options'] = $variant->weather_conditions;
                $trackInfo['tags'] = $variant->tags ? $variant->tags->pluck('name')->toArray() : [];
            }

            if ($groupByLocation) {
                $tracks[$variant->track->name][] = $trackInfo;
            } else {
                $tracks[] = $trackInfo;
            }
        }

        // Format response as human-readable text for the AI
        $totalTracks = $groupByLocation ? array_sum(array_map('count', $tracks)) : count($tracks);
        $output = "Found {$totalTracks} tracks:\n\n";

        if ($groupByLocation) {
            foreach ($tracks as $locationName => $locationTracks) {
                $output .= "📍 {$locationName} (" . count($locationTracks) . " tracks):\n";
                foreach ($locationTracks as $track) {
                    $output .= "  • {$track['variant_name']} ({$track['type']})\n";
                    $output .= "    ID: {$track['id']}\n";
                    if ($includeMetadata && !empty($track['tags'])) {
                        $output .= "    Tags: " . implode(', ', $track['tags']) . "\n";
                    }
                }
                $output .= "\n";
            }
        } else {
            // Group by type for easier reading
            $racingTracks = array_filter($tracks, fn($t) => $t['type'] === 'racing');
            $derbyTracks = array_filter($tracks, fn($t) => $t['type'] === 'derby');

            if (!empty($racingTracks)) {
                $output .= "🏁 RACING TRACKS (" . count($racingTracks) . "):\n";
                foreach ($racingTracks as $track) {
                    $output .= "  • {$track['display_name']}\n";
                    $output .= "    ID: {$track['id']}\n";
                    if ($includeMetadata && !empty($track['tags'])) {
                        $output .= "    Tags: " . implode(', ', $track['tags']) . "\n";
                    }
                }
                $output .= "\n";
            }

            if (!empty($derbyTracks)) {
                $output .= "💥 DERBY TRACKS (" . count($derbyTracks) . "):\n";
                foreach ($derbyTracks as $track) {
                    $output .= "  • {$track['display_name']}\n";
                    $output .= "    ID: {$track['id']}\n";
                    if ($includeMetadata && !empty($track['tags'])) {
                        $output .= "    Tags: " . implode(', ', $track['tags']) . "\n";
                    }
                }
            }
        }

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'group_by_location' => $schema->boolean()
                ->description('Group tracks by location/venue. Default is false.'),
            'include_metadata' => $schema->boolean()
                ->description('Include additional metadata like weather options, tags, and location keys. Default is true.'),
        ];
    }
}
