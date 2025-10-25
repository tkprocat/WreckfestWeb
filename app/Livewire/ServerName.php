<?php

namespace App\Livewire;

use App\Services\WreckfestApiClient;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ServerName extends Component
{
    public string $serverName = 'Wreckfest Web';
    public string $serverNamePlain = 'Wreckfest Web';
    public string $serverNameHtml = 'Wreckfest Web';

    public function mount(WreckfestApiClient $apiClient)
    {
        // Try to get from cache first
        $cached = Cache::get('server_name_data');
        if ($cached) {
            $this->serverName = $cached['name'];
            $this->serverNamePlain = $cached['plain'];
            $this->serverNameHtml = $cached['html'];
        }

        // Then try to update from API
        try {
            $serverConfig = $apiClient->getServerConfig();
            $this->serverName = $serverConfig['serverName'] ?? 'Wreckfest Web';
            $this->serverNamePlain = $this->stripColorCodes($this->serverName);
            $this->serverNameHtml = $this->formatColorCodes($this->serverName);

            // Cache for 5 minutes
            Cache::put('server_name_data', [
                'name' => $this->serverName,
                'plain' => $this->serverNamePlain,
                'html' => $this->serverNameHtml,
            ], now()->addMinutes(5));
        } catch (\Exception $e) {
            // If no cache and API fails, use default with gray styling
            if (!$cached) {
                $this->serverName = 'Wreckfest Web';
                $this->serverNamePlain = 'Wreckfest Web';
                $this->serverNameHtml = '<span class="text-gray-400">Wreckfest Web</span>';
            }
            // Otherwise keep the cached values (already set above)
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

    public function placeholder()
    {
        // Use cached server name in placeholder if available
        $cached = Cache::get('server_name_data');
        $displayHtml = $cached ? $cached['html'] : '<span class="text-gray-400 animate-pulse">Wreckfest Web</span>';

        return <<<HTML
        <div>
            <h1 class="text-4xl font-black uppercase tracking-wider"
                style="font-family: Impact, 'Arial Black', sans-serif; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);">
                {$displayHtml}
            </h1>
            <p class="text-gray-400 mt-1 font-semibold tracking-wide">WRECKFEST DEDICATED SERVER</p>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.server-name');
    }
}
