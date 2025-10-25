<?php

namespace App\Livewire;

use App\Services\WreckfestApiClient;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ServerStatus extends Component
{
    public bool $isRunning = false;

    public function mount(WreckfestApiClient $apiClient)
    {
        try {
            $status = $apiClient->getServerStatus();
            $this->isRunning = $status['isRunning'] ?? false;
        } catch (\Exception $e) {
            $this->isRunning = false;
        }
    }

    public function placeholder()
    {
        return view('livewire.server-status-placeholder');
    }

    public function render()
    {
        return view('livewire.server-status');
    }
}
