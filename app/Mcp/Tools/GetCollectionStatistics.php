<?php

namespace App\Mcp\Tools;

use App\Models\TrackCollection;
use App\Models\TrackVariant;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetCollectionStatistics extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Get detailed statistics about a track collection including track types, tag distribution, locations, and metadata completeness.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $collectionId = $request->get('id');
        $collectionName = $request->get('name');

        // Validate required parameters
        if (!$collectionId && !$collectionName) {
            return Response::text("Error: Either 'id' or 'name' parameter is required.");
        }

        // Find the collection
        $query = TrackCollection::query();
        if ($collectionId) {
            $query->where('id', $collectionId);
        } else {
            $query->where('name', 'LIKE', "%{$collectionName}%");
        }

        $collection = $query->first();

        if (!$collection) {
            $identifier = $collectionId ?? $collectionName;
            return Response::text("Error: Collection not found with identifier: {$identifier}");
        }

        $tracks = $collection->tracks;
        $trackCount = count($tracks);

        // Initialize statistics
        $racingCount = 0;
        $derbyCount = 0;
        $locations = [];
        $tags = [];
        $gamemodes = [];
        $lapCounts = [];
        $weatherSettings = [];
        $metadataComplete = 0;

        foreach ($tracks as $track) {
            $trackId = $track['track'] ?? null;

            if (!$trackId) {
                continue;
            }

            // Get track variant from database
            $variant = TrackVariant::with(['track', 'tags'])->where('variant_id', $trackId)->first();

            if ($variant) {
                // Count by type
                if ($variant->game_mode === 'Derby') {
                    $derbyCount++;
                } else {
                    $racingCount++;
                }

                // Count by location
                $location = $variant->track->name ?? 'Unknown';
                $locations[$location] = ($locations[$location] ?? 0) + 1;

                // Count tags
                foreach ($variant->tags as $tag) {
                    $tags[$tag->name] = ($tags[$tag->name] ?? 0) + 1;
                }
            }

            // Count gamemodes
            if (isset($track['gamemode']) && $track['gamemode']) {
                $gamemodes[$track['gamemode']] = ($gamemodes[$track['gamemode']] ?? 0) + 1;
            }

            // Count lap settings
            if (isset($track['laps']) && $track['laps'] !== null) {
                $lapCounts[$track['laps']] = ($lapCounts[$track['laps']] ?? 0) + 1;
            }

            // Count weather settings
            if (isset($track['weather']) && $track['weather']) {
                $weatherSettings[$track['weather']] = ($weatherSettings[$track['weather']] ?? 0) + 1;
            }

            // Check metadata completeness
            if (isset($track['gamemode']) && $track['gamemode'] && isset($track['laps']) && $track['laps'] !== null) {
                $metadataComplete++;
            }
        }

        // Sort by count (descending)
        arsort($locations);
        arsort($tags);
        arsort($gamemodes);
        arsort($lapCounts);

        // Build output
        $output = "📊 Statistics for '{$collection->name}'\n";
        $output .= "=" . str_repeat("=", strlen($collection->name) + 22) . "\n\n";

        $output .= "**OVERVIEW**\n";
        $output .= "Total tracks: {$trackCount}\n";
        $output .= "Racing tracks: {$racingCount} (" . round(($racingCount / max($trackCount, 1)) * 100) . "%)\n";
        $output .= "Derby tracks: {$derbyCount} (" . round(($derbyCount / max($trackCount, 1)) * 100) . "%)\n";
        $output .= "Metadata completeness: {$metadataComplete}/{$trackCount} (" . round(($metadataComplete / max($trackCount, 1)) * 100) . "%)\n\n";

        if (!empty($locations)) {
            $output .= "**TOP LOCATIONS**\n";
            $count = 0;
            foreach ($locations as $location => $num) {
                if ($count++ >= 5) break;
                $output .= "  • {$location}: {$num} track(s)\n";
            }
            if (count($locations) > 5) {
                $output .= "  ... and " . (count($locations) - 5) . " more locations\n";
            }
            $output .= "\n";
        }

        if (!empty($tags)) {
            $output .= "**TOP TAGS**\n";
            $count = 0;
            foreach ($tags as $tag => $num) {
                if ($count++ >= 10) break;
                $output .= "  • {$tag}: {$num} track(s)\n";
            }
            if (count($tags) > 10) {
                $output .= "  ... and " . (count($tags) - 10) . " more tags\n";
            }
            $output .= "\n";
        }

        if (!empty($gamemodes)) {
            $output .= "**GAMEMODE DISTRIBUTION**\n";
            foreach ($gamemodes as $gamemode => $num) {
                $output .= "  • {$gamemode}: {$num} track(s)\n";
            }
            $output .= "\n";
        }

        if (!empty($lapCounts)) {
            $output .= "**LAP COUNT DISTRIBUTION**\n";
            foreach ($lapCounts as $laps => $num) {
                $output .= "  • {$laps} laps: {$num} track(s)\n";
            }
            $output .= "\n";
        }

        if (!empty($weatherSettings)) {
            $output .= "**WEATHER SETTINGS**\n";
            foreach ($weatherSettings as $weather => $num) {
                $output .= "  • {$weather}: {$num} track(s)\n";
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
            'id' => $schema->integer()
                ->description('The collection ID. Either id or name must be provided.'),
            'name' => $schema->string()
                ->description('The collection name to search for. Either id or name must be provided.'),
        ];
    }
}
