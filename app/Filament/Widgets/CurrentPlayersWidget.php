<?php

namespace App\Filament\Widgets;

use App\Services\WreckfestApiClient;
use Filament\Widgets\Widget;

class CurrentPlayersWidget extends Widget
{
    protected static string $view = 'filament.widgets.current-players-widget';

    protected int | string | array $columnSpan = 'full';

    public ?array $players = null;

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    public function mount(): void
    {
        $this->refreshPlayers();
    }

    public function refreshPlayers(): void
    {
        $apiClient = app(WreckfestApiClient::class);
        $this->players = $apiClient->getPlayers();
    }

    public static function canView(): bool
    {
        return true;
    }

    public function formatPlayerName($player): string
    {
        $isBot = false;
        $playerName = 'Unknown Player';

        if (is_array($player)) {
            $isBot = ($player['isBot'] ?? $player['IsBot'] ?? false) === true;
            $playerName = $player['name'] ?? 'Unknown Player';
        } else {
            $playerName = $player;
        }

        return $isBot ? '(BOT) ' . $playerName : $playerName;
    }

    public function isBot($player): bool
    {
        if (is_array($player)) {
            return ($player['isBot'] ?? $player['IsBot'] ?? false) === true;
        }

        return false;
    }
}
