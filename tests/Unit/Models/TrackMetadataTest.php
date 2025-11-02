<?php

namespace Tests\Unit\Models;

use App\Models\TrackMetadata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrackMetadataTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function it_has_fillable_attributes(): void
    {
        $fillable = (new TrackMetadata)->getFillable();

        $this->assertContains('track_id', $fillable);
        $this->assertContains('tags', $fillable);
    }

    #[Test]
    public function it_casts_tags_to_array(): void
    {
        $casts = (new TrackMetadata)->getCasts();

        $this->assertArrayHasKey('tags', $casts);
        $this->assertEquals('array', $casts['tags']);
    }

    #[Test]
    public function it_can_store_and_retrieve_tags(): void
    {
        $metadata = TrackMetadata::factory()->create([
            'track_id' => 'test_track',
            'tags' => ['Dirt Road', 'City Streets'],
        ]);

        $this->assertIsArray($metadata->tags);
        $this->assertCount(2, $metadata->tags);
        $this->assertContains('Dirt Road', $metadata->tags);
        $this->assertContains('City Streets', $metadata->tags);

        // Refresh from database
        $metadata->refresh();

        $this->assertIsArray($metadata->tags);
        $this->assertCount(2, $metadata->tags);
    }

    #[Test]
    public function it_can_have_null_tags(): void
    {
        $metadata = TrackMetadata::factory()->create([
            'track_id' => 'test_track_no_tags',
            'tags' => null,
        ]);

        $this->assertNull($metadata->tags);
    }
}
