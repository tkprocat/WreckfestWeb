<?php

namespace App\Livewire;

use App\Services\WreckfestApiClient;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

class PlayerList extends Component
{
    public $players = [];
    public $maxPlayers = 24;

    public function mount(WreckfestApiClient $apiClient)
    {
        try {
            $serverConfig = $apiClient->getServerConfig();
            $this->players = $apiClient->getPlayers() ?? [];
            $this->maxPlayers = $serverConfig['maxPlayers'] ?? 24;
        } catch (\Exception $e) {
            $this->players = [];
        }
    }

    #[On('echo:server-updates,.players.updated')]
    public function playersUpdated($event)
    {
        \Log::info('Livewire PlayerList: Received players.updated event', ['count' => count($event['players'])]);

        // Replace entire player list with the updated list from the server
        $this->players = $event['players'];

        \Log::info('Livewire PlayerList: Updated player list', ['total' => count($this->players)]);
    }

    public function placeholder()
    {
        return view('livewire.player-list-placeholder');
    }

    public function render()
    {
        return view('livewire.player-list');
    }
}
