<?php

namespace Tests\Unit\Models;

use App\Models\TrackCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrackCollectionTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = (new TrackCollection)->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('tracks', $fillable);
    }

    #[Test]
    public function it_uses_attribute_accessor_for_tracks(): void
    {
        // Tracks uses Attribute accessor, not cast
        // Verify that it still returns array type
        $collection = new TrackCollection(['name' => 'Test', 'tracks' => []]);

        $this->assertIsArray($collection->tracks);
    }

    #[Test]
    public function it_can_store_and_retrieve_tracks(): void
    {
        $tracks = ['track1', 'track2', 'track3'];

        $collection = TrackCollection::create([
            'name' => 'Test Collection',
            'tracks' => $tracks,
        ]);

        $this->assertIsArray($collection->tracks);
        $this->assertCount(3, $collection->tracks);

        // Tracks are normalized to objects with proper structure
        $this->assertArrayHasKey('track', $collection->tracks[0]);
        $this->assertEquals('track1', $collection->tracks[0]['track']);

        // Refresh from database
        $collection->refresh();

        $this->assertIsArray($collection->tracks);
        $this->assertCount(3, $collection->tracks);
        $this->assertArrayHasKey('track', $collection->tracks[0]);
        $this->assertEquals('track1', $collection->tracks[0]['track']);
    }

    #[Test]
    public function it_can_have_empty_tracks_array(): void
    {
        $collection = TrackCollection::create([
            'name' => 'Empty Collection',
            'tracks' => [],
        ]);

        $this->assertIsArray($collection->tracks);
        $this->assertCount(0, $collection->tracks);
    }
}
