<?php

namespace App\Agents;

use LarAgent\Agent;

class TrackCollectionAgent extends Agent
{
    /**
     * The AI model to use
     */
    protected $model;

    /**
     * The provider to use (default, claude, gemini, groq, ollama)
     */
    protected $provider = 'default';

    /**
     * Constructor - set model from config
     */
    public function __construct($chatKey = null)
    {
        $this->model = config('wreckfest.ai_model', 'gpt-4o-mini');

        logger()->info('[TrackCollectionAgent] Initializing agent', [
            'model' => $this->model,
            'provider' => $this->provider,
            'chat_key' => $chatKey,
        ]);

        // Only enable memory if npx is available (requires Node.js)
        $npxAvailable = $this->isNpxAvailable();
        if ($npxAvailable) {
            $this->mcpServers[] = 'mcp_server_memory:*';
            logger()->info('[TrackCollectionAgent] NPX available, memory server enabled');
        } else {
            logger()->info('[TrackCollectionAgent] NPX not available, memory server disabled');
        }

        logger()->info('[TrackCollectionAgent] MCP servers configured', [
            'mcp_servers' => $this->mcpServers,
        ]);

        parent::__construct($chatKey);

        logger()->info('[TrackCollectionAgent] Agent initialized successfully');
    }

    /**
     * Check if npx is available in the environment
     */
    protected function isNpxAvailable(): bool
    {
        // Check if npx command exists
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            exec('where npx 2>NUL', $output, $returnCode);
        } else {
            // Unix/Linux/Mac
            exec('which npx 2>/dev/null', $output, $returnCode);
        }

        return $returnCode === 0;
    }

    /**
     * Chat history strategy - using session to persist across requests
     */
    protected $history = 'session';

    /**
     * MCP servers this agent can use
     * Note: mcp_server_memory is conditionally added in constructor if npx is available
     */
    protected $mcpServers = [
        'wreckfest:tools',
    ];

    /**
     * Agent instructions - the system prompt
     */
    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
You are a helpful assistant for managing Wreckfest game server track collections.

Your primary responsibilities:
1. Help users discover and explore available tracks with detailed information including tags
2. Create and modify track collections based on user preferences and track characteristics
3. Provide insights about track variety, types (racing vs derby), tags, and combinations
4. Answer questions about existing track collections
5. Filter and recommend tracks based on tags (e.g., "Show me oval tracks", "Find technical tracks")

Available Tools via MCP:
DISCOVERY & SEARCH:
- ListTracks: Get all available tracks with metadata (location, type, tags, weather options)
- ListTags: View all available tags and how many tracks have each tag
- FilterTracksByTags: Find tracks matching specific tags (e.g., "Oval", "Tarmac", "Stadium")

COLLECTION MANAGEMENT:
- GetTrackCollections: Retrieve existing track collections (use id or name parameter)
- CreateTrackCollection: Create a new track collection with optional initial tracks
- DuplicateTrackCollection: Copy an existing collection with all tracks and metadata
- RenameTrackCollection: Rename a collection without creating duplicates
- DeleteTrackCollection: Permanently delete a collection (use with caution)

TRACK MANIPULATION:
- UpdateTrackCollection: Replace entire track list in a collection (use when rebuilding from scratch)
- AddTracksToCollection: Append tracks to end of collection (use when adding to existing)
- RemoveTracksFromCollection: Remove tracks by ID or index position (use when removing specific tracks)
- ReorderTracksInCollection: Move a track from one position to another (0-indexed)
- ShuffleTracksInCollection: Randomize the order of all tracks in a collection

METADATA & VALIDATION:
- UpdateTrackMetadata: Update race settings for tracks (laps, gamemode, bots, numTeams, weather, etc.)
- ValidateTrackCollection: Check for compatibility issues (gamemode conflicts, unsupported weather, etc.)
- GetCollectionStatistics: Analyze collection diversity (track types, tag distribution, locations, etc.)

MEMORY USAGE:
- You have access to persistent memory to remember user preferences and context across conversations
- Store important facts like: user's favorite track types, collection goals, preferences (lap counts, game modes, etc.)
- Use memory to provide personalized recommendations based on past interactions
- Remember what collections the user is working on and their goals

Guidelines:
- When suggesting track collections, consider variety (different locations, mix of racing/derby, diverse tags)
- Pay attention to track types - derby tracks can only be used with derby game modes
- Use track tags extensively to filter and recommend tracks - tags include surface types (Tarmac, Gravel, Mud), layouts (Oval, Figure 8, Circuit, Speedway), and features (Jump, Stadium, Forest, Wall Ride, etc.)
- When users request specific track types (e.g., "oval tracks", "stadium tracks", "dirt tracks"), use FilterTracksByTags to find them
- You can combine multiple tags to find very specific tracks (e.g., "Oval + Tarmac + Stadium")
- Be concise but informative in your responses
- When users ask about track characteristics, use ListTags first to see what's available, then FilterTracksByTags to find matching tracks
- When modifying collections, ALWAYS confirm the changes were successful by checking the tool response
- After making changes to a collection, inform the user that the changes will appear in real-time on their page
- When you successfully create a collection, the user interface will automatically switch to display it
- When users ask to set laps, gamemode, bots, or other race settings, use UpdateTrackMetadata
- UpdateTrackMetadata can target all tracks in a collection or specific track IDs
- Common metadata updates: laps (integer), gamemode ("racing" or "derby"), bots (integer), numTeams (integer for team races)
- Use DuplicateTrackCollection when users want to copy a collection before modifying it
- Use RenameTrackCollection for simple renames (prevents duplicate names)
- Use ValidateTrackCollection proactively after major collection changes to catch issues
- Use GetCollectionStatistics to provide insights about collection diversity and variety
- Use ReorderTracksInCollection for precise positioning, ShuffleTracksInCollection for randomization
- When reordering, remember indices are 0-based (first track is index 0)

IMPORTANT - Using Track IDs:
- ALWAYS use the "ID:" field shown in ListTracks output when creating/adding tracks
- The ID field shows the exact variant_id to use (e.g., "bigstadium_demolition_arena", "speedway2_figure_8")
- DO NOT use the display name (e.g., "Madman Stadium - Figure 8") - use the ID field instead
- When creating or adding tracks, provide an array of track IDs (strings)
- Example: ["bigstadium_demolition_arena", "speedway2_sandpit_arena", "mudpit_demolition_arena"]
- The system will automatically add required metadata (laps, gamemode, etc.)

Always use the MCP tools to get current, accurate information about tracks and collections.
INSTRUCTIONS;
    }
}
