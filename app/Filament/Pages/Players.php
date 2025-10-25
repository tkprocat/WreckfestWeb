<?php

namespace App\Filament\Pages;

use App\Services\WreckfestApiClient;
use Filament\Pages\Page;

class Players extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Current Players';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.players';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $players = null;

    public function mount(): void
    {
        $this->refreshPlayers();
    }

    public function refreshPlayers(): void
    {
        $apiClient = app(WreckfestApiClient::class);
        $this->players = $apiClient->getPlayers();
    }
}
