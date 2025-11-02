<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\AddTracksToCollection;
use App\Mcp\Tools\CreateTrackCollection;
use App\Mcp\Tools\DeleteTrackCollection;
use App\Mcp\Tools\GetTrackCollections;
use App\Mcp\Tools\ListTracks;
use App\Mcp\Tools\RemoveTracksFromCollection;
use App\Mcp\Tools\UpdateTrackCollection;
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
    protected string $version = '0.0.2';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = "This server allows you to manage Wreckfest game server track collections. You can list available tracks with detailed metadata, retrieve track collections, create new collections, modify them, and delete them. Available operations: ListTracks (view all tracks), GetTrackCollections (view collections), CreateTrackCollection (create new collection), UpdateTrackCollection (replace all tracks in a collection), AddTracksToCollection (append tracks to a collection), RemoveTracksFromCollection (remove tracks by ID or index), DeleteTrackCollection (permanently delete a collection).";

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        ListTracks::class,
        GetTrackCollections::class,
        CreateTrackCollection::class,
        UpdateTrackCollection::class,
        AddTracksToCollection::class,
        RemoveTracksFromCollection::class,
        DeleteTrackCollection::class,
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
