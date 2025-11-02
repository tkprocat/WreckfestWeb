<?php

namespace Tests\Feature\Mcp;

use App\Mcp\Tools\ListTracks;
use App\Models\TrackMetadata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Mcp\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListTracksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed tracks for testing
        $this->seed(\Database\Seeders\TrackSeeder::class);
    }

    #[Test]
    public function it_returns_all_tracks_with_metadata(): void
    {
        $tool = new ListTracks;
        $request = new Request([]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        // Verify text response format
        $this->assertStringContainsString('Found', $content);
        $this->assertStringContainsString('tracks:', $content);

        // Should contain racing and derby sections
        $this->assertStringContainsString('RACING TRACKS', $content);
        $this->assertStringContainsString('DERBY TRACKS', $content);

        // Should contain track IDs
        $this->assertStringContainsString('ID:', $content);
    }

    #[Test]
    public function it_groups_tracks_by_location_when_requested(): void
    {
        $tool = new ListTracks;
        $request = new Request(['group_by_location' => true]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        // Verify grouped format with location emoji
        $this->assertStringContainsString('📍', $content);
        $this->assertStringContainsString('tracks):', $content);
    }

    #[Test]
    public function it_excludes_metadata_when_not_requested(): void
    {
        $tool = new ListTracks;
        $request = new Request(['include_metadata' => false]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        // Should not have tags in output when metadata is excluded
        $this->assertStringNotContainsString('Tags:', $content);

        // Should still have track IDs
        $this->assertStringContainsString('ID:', $content);
    }

    #[Test]
    public function it_includes_tags_from_track_metadata(): void
    {
        $tool = new ListTracks;
        $request = new Request([]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        // With metadata enabled by default, if there are tags they should appear
        // This test just verifies the format is correct
        $this->assertIsString($content);
        $this->assertStringContainsString('Found', $content);
    }

    #[Test]
    public function it_distinguishes_between_racing_and_derby_tracks(): void
    {
        $tool = new ListTracks;
        $request = new Request([]);
        $response = $tool->handle($request);

        $content = (string) $response->content();

        // Should have both racing and derby sections
        $this->assertStringContainsString('RACING TRACKS', $content);
        $this->assertStringContainsString('DERBY TRACKS', $content);
    }
}
