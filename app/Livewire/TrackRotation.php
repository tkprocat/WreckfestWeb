<?php

namespace App\Livewire;

use App\Helpers\TrackHelper;
use App\Services\WreckfestApiClient;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class TrackRotation extends Component
{
    public $trackRotation = [];

    public $currentTrack = '';

    public $collectionName = '';

    public function mount(WreckfestApiClient $apiClient)
    {
        try {
            $tracks = $apiClient->getTrackRotation() ?? [];
            $this->trackRotation = $this->enrichTracksWithDetails($tracks);
            $this->currentTrack = $apiClient->getCurrentTrack() ?? '';
            $this->collectionName = $apiClient->getCollectionName() ?? '';
        } catch (\Exception $e) {
            $this->trackRotation = [];
            $this->currentTrack = '';
            $this->collectionName = '';
        }
    }

    #[On('echo:server-updates,track.changed')]
    public function trackChanged($event)
    {
        $this->currentTrack = $event['trackId'] ?? '';
    }

    /**
     * Enrich track data with human-readable information from config
     */
    private function enrichTracksWithDetails(array $tracks): array
    {
        return array_map(function ($track) {
            $trackId = $track['track'] ?? '';
            $gamemode = $track['gamemode'] ?? '';

            // Get track details using TrackHelper
            $trackDetails = TrackHelper::getTrackDetails($trackId);

            return array_merge($track, [
                'trackName' => $trackDetails['variant'],
                'locationName' => $trackDetails['location'],
                'fullTrackName' => $trackDetails['fullName'],
                'gamemodeName' => $this->getGamemodeName($gamemode),
            ]);
        }, $tracks);
    }

    /**
     * Get human-readable gamemode name
     */
    private function getGamemodeName(string $gamemode): string
    {
        $gamemodes = [
            'racing' => 'Racing',
            'derby' => 'Derby',
            'derbydeathmatch' => 'Derby Deathmatch',
            'teamdeathmatch' => 'Team Deathmatch',
            'teamrace' => 'Team Race',
            'eliminationrace' => 'Elimination Race',
        ];

        return $gamemodes[strtolower($gamemode)] ?? $gamemode;
    }

    public function placeholder()
    {
        return view('livewire.track-rotation-placeholder');
    }

    public function render()
    {
        return view('livewire.track-rotation');
    }
}
