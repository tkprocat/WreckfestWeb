<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AddTracksToCollection;
use App\Mcp\Tools\CreateTrackCollection;
use App\Mcp\Tools\DeleteTrackCollection;
use App\Mcp\Tools\DuplicateTrackCollection;
use App\Mcp\Tools\FilterTracksByTags;
use App\Mcp\Tools\GetCollectionStatistics;
use App\Mcp\Tools\GetTrackCollections;
use App\Mcp\Tools\ListTags;
use App\Mcp\Tools\ListTracks;
use App\Mcp\Tools\RemoveTracksFromCollection;
use App\Mcp\Tools\RenameTrackCollection;
use App\Mcp\Tools\ReorderTracksInCollection;
use App\Mcp\Tools\ShuffleTracksInCollection;
use App\Mcp\Tools\UpdateTrackCollection;
use App\Mcp\Tools\UpdateTrackMetadata;
use App\Mcp\Tools\ValidateTrackCollection;
use Laravel\Mcp\Server;

class Wreckfest extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Wreckfest';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.5';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = "This server allows you to manage Wreckfest game server track collections comprehensively. DISCOVERY: ListTracks (view all tracks with metadata and tags), ListTags (view all available tags), FilterTracksByTags (find tracks by characteristics like Oval, Tarmac, Stadium). COLLECTION MANAGEMENT: GetTrackCollections (view collections), CreateTrackCollection (create new), DuplicateTrackCollection (copy existing), RenameTrackCollection (rename), DeleteTrackCollection (delete). TRACK MANIPULATION: UpdateTrackCollection (replace all tracks), AddTracksToCollection (append tracks), RemoveTracksFromCollection (remove by ID or index), ReorderTracksInCollection (move tracks to specific positions), ShuffleTracksInCollection (randomize order). METADATA & VALIDATION: UpdateTrackMetadata (set laps, gamemode, bots, weather, etc.), ValidateTrackCollection (check compatibility issues), GetCollectionStatistics (analyze variety and distribution).";

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        // Discovery & Search
        ListTracks::class,
        ListTags::class,
        FilterTracksByTags::class,

        // Collection Management
        GetTrackCollections::class,
        CreateTrackCollection::class,
        DuplicateTrackCollection::class,
        RenameTrackCollection::class,
        DeleteTrackCollection::class,

        // Track Manipulation
        UpdateTrackCollection::class,
        AddTracksToCollection::class,
        RemoveTracksFromCollection::class,
        ReorderTracksInCollection::class,
        ShuffleTracksInCollection::class,

        // Metadata & Validation
        UpdateTrackMetadata::class,
        ValidateTrackCollection::class,
        GetCollectionStatistics::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
