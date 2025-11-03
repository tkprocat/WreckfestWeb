<?php

namespace App\Mcp\Tools;

use App\Helpers\TrackHelper;
use App\Models\TrackCollection;
use App\Models\TrackVariant;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ValidateTrackCollection extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "Validate a track collection for compatibility issues, checking gamemode compatibility, weather settings, and track metadata.";

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
        $issues = [];
        $warnings = [];
        $validCount = 0;

        foreach ($tracks as $index => $track) {
            $trackId = $track['track'] ?? null;
            $gamemode = $track['gamemode'] ?? null;
            $weather = $track['weather'] ?? null;

            if (!$trackId) {
                $issues[] = "Track #{$index}: Missing track ID";
                continue;
            }

            // Check if track exists in database
            $variant = TrackVariant::where('variant_id', $trackId)->first();
            if (!$variant) {
                $issues[] = "Track #{$index} ({$trackId}): Track not found in database";
                continue;
            }

            // Check gamemode compatibility
            if ($gamemode) {
                if (!TrackHelper::isTrackCompatibleWithGamemode($trackId, $gamemode)) {
                    $isDerbyTrack = TrackHelper::isDerbyOnlyTrack($trackId);
                    $trackType = $isDerbyTrack ? 'derby' : 'racing';
                    $gamemodeType = TrackHelper::isDerbyGamemode($gamemode) ? 'derby' : 'racing';

                    $issues[] = "Track #{$index} ({$variant->full_name}): Incompatible gamemode - track is for {$trackType} but gamemode '{$gamemode}' is for {$gamemodeType}";
                }
            }

            // Check weather compatibility
            if ($weather && $weather !== 'random') {
                if (!TrackHelper::isWeatherSupportedForTrack($trackId, $weather)) {
                    $supportedWeather = TrackHelper::getSupportedWeatherForTrack($trackId);
                    $warnings[] = "Track #{$index} ({$variant->full_name}): Weather '{$weather}' may not be supported. Supported weather: " . implode(', ', $supportedWeather);
                }
            }

            // Check for missing recommended metadata
            if (!$gamemode) {
                $warnings[] = "Track #{$index} ({$variant->full_name}): No gamemode set";
            }

            if (!isset($track['laps']) || $track['laps'] === null) {
                $warnings[] = "Track #{$index} ({$variant->full_name}): No lap count set";
            }

            if (empty($issues)) {
                $validCount++;
            }
        }

        // Build output
        $output = "Validation Report for '{$collection->name}'\n";
        $output .= "=" . str_repeat("=", strlen($collection->name) + 26) . "\n\n";
        $output .= "Total tracks: " . count($tracks) . "\n";
        $output .= "Valid tracks: {$validCount}\n";
        $output .= "Issues found: " . count($issues) . "\n";
        $output .= "Warnings: " . count($warnings) . "\n\n";

        if (empty($issues) && empty($warnings)) {
            $output .= "✓ Collection is valid! No issues or warnings found.\n";
        } else {
            if (!empty($issues)) {
                $output .= "🚫 ISSUES (must fix):\n";
                foreach ($issues as $issue) {
                    $output .= "  • {$issue}\n";
                }
                $output .= "\n";
            }

            if (!empty($warnings)) {
                $output .= "⚠️  WARNINGS (recommended to fix):\n";
                foreach ($warnings as $warning) {
                    $output .= "  • {$warning}\n";
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
            'id' => $schema->integer()
                ->description('The collection ID. Either id or name must be provided.'),
            'name' => $schema->string()
                ->description('The collection name to search for. Either id or name must be provided.'),
        ];
    }
}
