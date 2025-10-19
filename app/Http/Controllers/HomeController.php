<?php

namespace App\Http\Controllers;

use App\Exceptions\WreckfestApiException;
use App\Services\WreckfestApiClient;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected WreckfestApiClient $apiClient;

    public function __construct(WreckfestApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function index()
    {
        try {
            $serverConfig = $this->apiClient->getServerConfig();
            $serverStatus = $this->apiClient->getServerStatus();
            $players = $this->apiClient->getPlayers();

            $serverName = $serverConfig['serverName'] ?? 'Wreckfest Server';

            return view('home', [
                'serverName' => $serverName,
                'serverNamePlain' => $this->stripColorCodes($serverName),
                'serverNameHtml' => $this->formatColorCodes($serverName),
                'serverStatus' => $serverStatus,
                'players' => $players,
                'maxPlayers' => $serverConfig['maxPlayers'] ?? 24,
                'apiError' => false,
            ]);
        } catch (WreckfestApiException $e) {
            return view('home', [
                'serverName' => 'Wreckfest Server',
                'serverNamePlain' => 'Wreckfest Server',
                'serverNameHtml' => 'Wreckfest Server',
                'serverStatus' => [],
                'players' => [],
                'maxPlayers' => 24,
                'apiError' => true,
            ]);
        }
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
                    $result .= '<span style="color: ' . $colors[$currentColor] . '">';
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
