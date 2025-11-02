<?php

namespace App\Mcp\Tools;

use App\Models\Tag;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class ListTags extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = "List all available track tags. Returns tag names, slugs, colors, and the number of tracks associated with each tag. Use this to discover what tags exist for filtering tracks.";

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        // Load all tags with track counts
        $tags = Tag::withCount('trackVariants')
            ->orderBy('name')
            ->get();

        if ($tags->isEmpty()) {
            return Response::text("No tags found in the system.");
        }

        $output = "Available Track Tags (" . $tags->count() . " total):\n\n";

        // Group tags by category based on common characteristics
        $layoutTags = $tags->filter(fn($tag) => in_array($tag->slug, ['oval', 'figure-8', 'circuit', 'speedway', 'wild']));
        $surfaceTags = $tags->filter(fn($tag) => in_array($tag->slug, ['gravel', 'tarmac', 'mud']));
        $specialtyTags = $tags->diff($layoutTags)->diff($surfaceTags);

        if ($layoutTags->isNotEmpty()) {
            $output .= "🏁 LAYOUT TAGS:\n";
            foreach ($layoutTags as $tag) {
                $output .= "  • {$tag->name} ({$tag->track_variants_count} tracks)\n";
            }
            $output .= "\n";
        }

        if ($surfaceTags->isNotEmpty()) {
            $output .= "🛣️ SURFACE TAGS:\n";
            foreach ($surfaceTags as $tag) {
                $output .= "  • {$tag->name} ({$tag->track_variants_count} tracks)\n";
            }
            $output .= "\n";
        }

        if ($specialtyTags->isNotEmpty()) {
            $output .= "⭐ SPECIALTY TAGS:\n";
            foreach ($specialtyTags as $tag) {
                $output .= "  • {$tag->name} ({$tag->track_variants_count} tracks)\n";
            }
        }

        $output .= "\nUse these tag names to filter tracks with the FilterTracksByTags tool.";

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
