<?php

namespace App\Filament\Widgets;

use App\Exceptions\WreckfestApiException;
use App\Services\WreckfestApiClient;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class CurrentPlayersWidget extends Widget
{
    protected string $view = 'filament.widgets.current-players-widget';

    protected int|string|array $columnSpan = 'full';

    public ?array $players = null;

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    public function mount(): void
    {
        $this->refreshPlayers();
    }

    public function refreshPlayers(): void
    {
        try {
            $apiClient = app(WreckfestApiClient::class);
            $this->players = $apiClient->getPlayers();
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to contact Wreckfest Controller')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->send();

            $this->players = null;
        }
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

        return $isBot ? '(BOT) '.$playerName : $playerName;
    }

    public function isBot($player): bool
    {
        if (is_array($player)) {
            return ($player['isBot'] ?? $player['IsBot'] ?? false) === true;
        }

        return false;
    }
}
