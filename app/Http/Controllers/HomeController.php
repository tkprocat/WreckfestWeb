<?php

namespace App\Http\Controllers;

use App\Helpers\TrackHelper;
use App\Services\WreckfestApiClient;

class HomeController extends Controller
{
    protected WreckfestApiClient $apiClient;

    public function __construct(WreckfestApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function index()
    {
        // No API calls - everything loads asynchronously via Livewire
        return view('home');
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

    /**
     * Strip Wreckfest color codes from text
     */
    private function stripColorCodes(string $text): string
    {
        return preg_replace('/\^\d/', '', $text);
    }

    /**
     * Convert Wreckfest color codes to HTML
     */
    private function formatColorCodes(string $text): string
    {
        $colors = [
            '1' => '#ff0000', // Red
            '2' => '#00ff00', // Green
            '3' => '#ff8800', // Orange
            '4' => '#0044ff', // Dark Blue
            '5' => '#00ccff', // Light Blue
            '6' => '#cc00ff', // Purple
            '7' => '#ffffff', // White
            '8' => '#888888', // Gray
            '9' => '#000000', // Black
        ];

        // Split by color codes while keeping them
        $parts = preg_split('/(\^\d)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        $currentColor = null;

        foreach ($parts as $part) {
            if (preg_match('/\^(\d)/', $part, $matches)) {
                // Close previous color if any
                if ($currentColor !== null) {
                    $result .= '</span>';
                }
                // Set new color
                $currentColor = $matches[1];
                if (isset($colors[$currentColor])) {
                    $result .= '<span style="color: '.$colors[$currentColor].'">';
                }
            } elseif ($part !== '') {
                // Regular text
                $result .= htmlspecialchars($part);
            }
        }

        // Close final span if any
        if ($currentColor !== null) {
            $result .= '</span>';
        }

        return $result;
    }
}
