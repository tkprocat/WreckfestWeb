<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Tools\GetTrackCollections;
use App\Models\TrackCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Mcp\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetTrackCollectionsTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_returns_all_collections(): void
    {
        TrackCollection::create(['name' => 'Racing Only', 'tracks' => ['track1', 'track2']]);
        TrackCollection::create(['name' => 'Derby Only', 'tracks' => ['track3']]);

        $tool = new GetTrackCollections;
        $request = new Request([]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        $this->assertStringContainsString('Found 2 track collections:', $content);
        $this->assertStringContainsString('Racing Only', $content);
        $this->assertStringContainsString('Derby Only', $content);
        $this->assertStringContainsString('Tracks: 2', $content);
        $this->assertStringContainsString('Tracks: 1', $content);
    }

    #[Test]
    public function it_returns_collection_by_id(): void
    {
        $collection = TrackCollection::create([
            'name' => 'Test Collection',
            'tracks' => ['track1', 'track2', 'track3'],
        ]);

        $tool = new GetTrackCollections;
        $request = new Request(['id' => $collection->id]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        $this->assertStringContainsString('Collection: Test Collection', $content);
        $this->assertStringContainsString('ID: ' . $collection->id, $content);
        $this->assertStringContainsString('Tracks: 3', $content);
        $this->assertStringContainsString('track1', $content);
        $this->assertStringContainsString('track2', $content);
        $this->assertStringContainsString('track3', $content);
    }

    #[Test]
    public function it_returns_collection_by_name(): void
    {
        TrackCollection::create([
            'name' => 'Mixed Modes',
            'tracks' => ['track1', 'track2'],
        ]);

        $tool = new GetTrackCollections;
        $request = new Request(['name' => 'Mixed Modes']);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        $this->assertStringContainsString('Collection: Mixed Modes', $content);
        $this->assertStringContainsString('Tracks: 2', $content);
        $this->assertStringContainsString('track1', $content);
        $this->assertStringContainsString('track2', $content);
    }

    #[Test]
    public function it_returns_error_for_non_existent_id(): void
    {
        $tool = new GetTrackCollections;
        $request = new Request(['id' => 9999]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        $this->assertStringContainsString('Error', $content);
        $this->assertStringContainsString('not found', $content);
        $this->assertStringContainsString('9999', $content);
    }

    #[Test]
    public function it_returns_error_for_non_existent_name(): void
    {
        $tool = new GetTrackCollections;
        $request = new Request(['name' => 'NonExistent Collection']);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        $this->assertStringContainsString('Error', $content);
        $this->assertStringContainsString('not found', $content);
        $this->assertStringContainsString('NonExistent Collection', $content);
    }

    #[Test]
    public function it_includes_timestamps_in_collection_data(): void
    {
        $collection = TrackCollection::create([
            'name' => 'Test Collection',
            'tracks' => [],
        ]);

        $tool = new GetTrackCollections;
        $request = new Request(['id' => $collection->id]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        $this->assertStringContainsString('Created:', $content);
        $this->assertStringContainsString('Updated:', $content);
    }

    #[Test]
    public function it_orders_collections_by_updated_at_desc(): void
    {
        $first = TrackCollection::create(['name' => 'First', 'tracks' => []]);
        sleep(1);
        $second = TrackCollection::create(['name' => 'Second', 'tracks' => []]);

        $tool = new GetTrackCollections;
        $request = new Request([]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        // Most recently updated should be first
        $secondPos = strpos($content, 'Second');
        $firstPos = strpos($content, 'First');

        $this->assertNotFalse($secondPos);
        $this->assertNotFalse($firstPos);
        $this->assertLessThan($firstPos, $secondPos, 'Second should appear before First in the output');
    }
}
