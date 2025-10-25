<?php

namespace App\Services;

use Exception;
use App\Exceptions\WreckfestApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class WreckfestApiClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('wreckfest.api_url', 'https://localhost:5101/api');
    }

    /**
     * Get server basic configuration
     *
     * @throws WreckfestApiException
     */
    public function getServerConfig(): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->get("{$this->baseUrl}/Config/basic");
            return $response->successful() ? $response->json() : [];
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to get server config: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Update server basic configuration
     *
     * @throws WreckfestApiException
     */
    public function updateServerConfig(array $config): bool
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->put("{$this->baseUrl}/Config/basic", $config);

            if (!$response->successful()) {
                Log::error('Failed to update server config - API returned status: ' . $response->status());
                Log::error('Response body: ' . $response->body());
            }

            return $response->successful();
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to update server config: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Get track collection name
     *
     * @throws WreckfestApiException
     */
    public function getTrackCollectionName(): ?string
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->get("{$this->baseUrl}/Config/tracks/collection-name");
            if ($response->successful()) {
                $data = $response->json();
                // Return the collection name if it exists
                return $data['collectionName'] ?? $data['collection_name'] ?? null;
            }
            return null;
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to get track collection name: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Get track rotation list
     *
     * @throws WreckfestApiException
     */
    public function getTracks(): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->get("{$this->baseUrl}/Config/tracks");
            if ($response->successful()) {
                $data = $response->json();
                // API returns structure: {"count":30,"tracks":[...]}
                // We need to extract just the tracks array
                if (isset($data['tracks']) && is_array($data['tracks'])) {
                    return $data['tracks'];
                }
                // Fallback if structure is different
                return is_array($data) ? $data : [];
            }
            return [];
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to get tracks: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Update entire track rotation list
     *
     * @throws WreckfestApiException
     */
    public function updateTracks(array $tracks): bool
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->put("{$this->baseUrl}/Config/tracks", $tracks);

            if (!$response->successful()) {
                Log::error('Failed to update tracks - API returned status: ' . $response->status());
                Log::error('Response body: ' . $response->body());
            }

            return $response->successful();
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to update tracks: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Add a new track to rotation
     */
    public function addTrack(array $track): bool
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->baseUrl}/Config/tracks", $track);
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to add track: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a specific track by index
     */
    public function updateTrack(int $index, array $track): bool
    {
        try {
            $response = Http::withoutVerifying()->put("{$this->baseUrl}/Config/tracks/{$index}", $track);
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to update track: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a track by index
     */
    public function deleteTrack(int $index): bool
    {
        try {
            $response = Http::withoutVerifying()->delete("{$this->baseUrl}/Config/tracks/{$index}");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to delete track: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get server status
     *
     * @throws WreckfestApiException
     */
    public function getServerStatus(): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->get("{$this->baseUrl}/Server/status");
            return $response->successful() ? $response->json() : [];
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to get server status: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Start the server
     */
    public function startServer(): bool
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->baseUrl}/Server/start");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to start server: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Stop the server
     */
    public function stopServer(): bool
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->baseUrl}/Server/stop");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to stop server: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restart the server
     */
    public function restartServer(): bool
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->baseUrl}/Server/restart");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to restart server: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Attach to an existing server process
     */
    public function attachToServer(int $pid): bool
    {
        try {
            $response = Http::withoutVerifying()->post("{$this->baseUrl}/Server/attach/{$pid}");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to attach to server: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get server log file
     *
     * @throws WreckfestApiException
     */
    public function getLogFile(int $lines = 100): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->get("{$this->baseUrl}/Server/logfile", ['lines' => $lines]);
            if ($response->successful()) {
                $data = $response->json();
                // API returns nested structure: {"lines":100,"source":"logfile","logFilePath":"...","output":[...]}
                // We need to extract just the output array
                if (isset($data['output']) && is_array($data['output'])) {
                    return $data['output'];
                }
                // Fallback if structure is different
                return is_array($data) ? $data : [];
            }
            return [];
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to get log file: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Get current players
     *
     * @throws WreckfestApiException
     */
    public function getPlayers(): array
    {
        try {
            $response = Http::withoutVerifying()->timeout(2)->get("{$this->baseUrl}/Server/players");
            if ($response->successful()) {
                $data = $response->json();
                // API returns nested structure: {"totalPlayers":0,"maxPlayers":24,"players":[],"lastUpdated":"..."}
                // We need to extract just the players array
                if (isset($data['players']) && is_array($data['players'])) {
                    return $data['players'];
                }
                // Fallback if structure is different
                return is_array($data) ? $data : [];
            }
            return [];
        } catch (ConnectionException $e) {
            Log::error('Failed to connect to Wreckfest API: ' . $e->getMessage());
            throw new WreckfestApiException();
        } catch (Exception $e) {
            Log::error('Failed to get players: ' . $e->getMessage());
            throw new WreckfestApiException();
        }
    }

    /**
     * Get track rotation list (alias for getTracks)
     *
     * @throws WreckfestApiException
     */
    public function getTrackRotation(): array
    {
        return $this->getTracks();
    }

    /**
     * Get collection name (alias for getTrackCollectionName)
     *
     * @throws WreckfestApiException
     */
    public function getCollectionName(): ?string
    {
        return $this->getTrackCollectionName();
    }

    /**
     * Get current track from server status
     *
     * @throws WreckfestApiException
     */
    public function getCurrentTrack(): ?string
    {
        try {
            $status = $this->getServerStatus();
            return $status['currentTrack'] ?? null;
        } catch (Exception $e) {
            Log::error('Failed to get current track: ' . $e->getMessage());
            return null;
        }
    }
}
