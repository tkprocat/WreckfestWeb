<?php

namespace App\Filament\Pages;

use App\Services\WreckfestApiClient;
use Filament\Pages\Page;

class ConfigPreview extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Config Preview';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.config-preview';

    public array $serverConfig = [];

    public array $trackRotation = [];

    public string $plainTextConfig = '';

    public function mount(): void
    {
        $apiClient = app(WreckfestApiClient::class);
        $this->serverConfig = $apiClient->getServerConfig();
        $this->trackRotation = $apiClient->getTracks();
        $this->generatePlainTextConfig();
    }

    public function refresh(): void
    {
        $apiClient = app(WreckfestApiClient::class);
        $this->serverConfig = $apiClient->getServerConfig();
        $this->trackRotation = $apiClient->getTracks();
        $this->generatePlainTextConfig();

        $this->dispatch('configRefreshed');
    }

    protected function generatePlainTextConfig(): void
    {
        $config = [];

        // Header
        $config[] = '# Wreckfest Server Configuration';
        $config[] = '# **************************************';
        $config[] = '';

        // Basic server info section
        $config[] = '# Set basic server info';
        $config[] = '# Character limits: server_name (63), welcome_message (254), password (31)';
        $config[] = 'server_name='.($this->serverConfig['serverName'] ?? '');
        $config[] = 'welcome_message='.($this->serverConfig['welcomeMessage'] ?? '');
        $config[] = 'password='.($this->serverConfig['password'] ?? '');
        $config[] = 'max_players='.($this->serverConfig['maxPlayers'] ?? '24');
        $config[] = '';

        // Server ports section
        $config[] = '# Set server ports';
        $config[] = '# Server is visible in LAN search only for query ports 27015-27020 and 26900-26905';
        $config[] = 'steam_port='.($this->serverConfig['steamPort'] ?? '27015');
        $config[] = 'game_port='.($this->serverConfig['gamePort'] ?? '33540');
        $config[] = 'query_port='.($this->serverConfig['queryPort'] ?? '27016');
        $config[] = '';

        // Quickplay exclusion
        $config[] = '# Server is excluded from being joined by users entering Quick Match';
        $config[] = '# 0 = not excluded, 1 = excluded';
        $config[] = 'exclude_from_quickplay='.(isset($this->serverConfig['excludeFromQuickplay']) ? ($this->serverConfig['excludeFromQuickplay'] ? '1' : '0') : '0');
        $config[] = '';

        // Admin control
        $config[] = '# When enabled, admin starts the countdown by setting themselves ready';
        $config[] = '# 0 = automatic countdown, 1 = admin starts countdown';
        $config[] = 'admin_control='.(isset($this->serverConfig['adminControl']) ? ($this->serverConfig['adminControl'] ? '1' : '0') : '0');
        $config[] = '';

        // Lobby settings
        $config[] = '# The duration of the countdown in seconds (allowed range 30 - 127)';
        $config[] = 'lobby_countdown='.($this->serverConfig['lobbyCountdown'] ?? '30');
        $config[] = '';
        $config[] = '# The percentage of players required to be ready to initiate automatic countdown, if enabled';
        $config[] = 'ready_players_required='.($this->serverConfig['readyPlayersRequired'] ?? '75');
        $config[] = '';

        // Admin Steam IDs
        $config[] = '# A comma separated list of Steam IDs (steamID64) of users that will be automatically granted admin privileges';
        $config[] = 'admin_steam_ids='.($this->serverConfig['adminSteamIds'] ?? '');
        $config[] = '';

        // Session mode
        $config[] = '# Set session mode, list available modes with command: sessionmodes';
        $config[] = 'session_mode='.($this->serverConfig['sessionMode'] ?? 'normal');
        $config[] = '';

        // Grid order
        $config[] = '# Set grid order, list available orders with command: gridorders';
        $config[] = 'grid_order='.($this->serverConfig['gridOrder'] ?? 'random');
        $config[] = '';

        // Track voting
        $config[] = '# Whether players will vote for the next event after event has ended';
        $config[] = '# 0 = no voting, 1 = use voting';
        $config[] = 'enable_track_vote='.(isset($this->serverConfig['enableTrackVote']) ? ($this->serverConfig['enableTrackVote'] ? '1' : '0') : '1');
        $config[] = '';

        // Idle kick
        $config[] = '# Whether to kick idling players while event is in progress';
        $config[] = '# 0 = kick idle players, 1 = do not kick idle players';
        $config[] = 'disable_idle_kick='.(isset($this->serverConfig['disableIdleKick']) ? ($this->serverConfig['disableIdleKick'] ? '1' : '0') : '0');
        $config[] = '';

        // Track and gamemode
        $config[] = '# Set track, list available track names with command: tracks';
        $config[] = 'track='.($this->serverConfig['track'] ?? '');
        $config[] = '';
        $config[] = '# Set game mode, list of available game modes:';
        $config[] = '# racing, derby, derby deathmatch, team derby, team race, elimination race';
        $config[] = 'gamemode='.($this->serverConfig['gamemode'] ?? 'racing');
        $config[] = '';

        // Bots and AI
        $config[] = '# Prepopulate server with AI bots, 0-24';
        $config[] = 'bots='.($this->serverConfig['bots'] ?? '0');
        $config[] = '';
        $config[] = '# Set AI difficulty between novice, amateur and expert';
        $config[] = 'ai_difficulty='.($this->serverConfig['aiDifficulty'] ?? 'amateur');
        $config[] = '';

        // Laps and time
        $config[] = '# Amount of laps in race game modes, 1-60';
        $config[] = 'laps='.($this->serverConfig['laps'] ?? '3');
        $config[] = '';
        $config[] = '# Deathmatch time limit in minutes';
        $config[] = 'time_limit='.($this->serverConfig['timeLimit'] ?? '5');
        $config[] = '';

        // Vehicle damage
        $config[] = '# Set vehicle damage to normal, intense, realistic or extreme';
        $config[] = 'vehicle_damage='.($this->serverConfig['vehicleDamage'] ?? 'normal');
        $config[] = '';

        // Car restrictions
        $config[] = '# Allow only vehicles with a maximum class of a, b, or c';
        $config[] = '# Leave blank for no restriction';
        $config[] = 'car_class_restriction='.($this->serverConfig['carClassRestriction'] ?? '');
        $config[] = '';
        $config[] = '# Allow only one specific car, list available cars with command: cars';
        $config[] = '# Leave blank for no restriction';
        $config[] = 'car_restriction='.($this->serverConfig['carRestriction'] ?? '');
        $config[] = '';

        // Special vehicles
        $config[] = '# Disallow use of special vehicles';
        $config[] = '# 0 = allowed, 1 = disallowed';
        $config[] = 'special_vehicles_disabled='.(isset($this->serverConfig['specialVehiclesDisabled']) ? ($this->serverConfig['specialVehiclesDisabled'] ? '1' : '0') : '0');
        $config[] = '';

        // Car reset
        $config[] = '# Disable car reset';
        $config[] = '# 0 = car reset enabled, 1 = car reset disabled';
        $config[] = 'car_reset_disabled='.(isset($this->serverConfig['carResetDisabled']) ? ($this->serverConfig['carResetDisabled'] ? '1' : '0') : '0');
        $config[] = '';

        // Wrong way limiter
        $config[] = '# Disable speed limiter for players that drive the wrong way';
        $config[] = '# 0 = speed limiter enabled, 1 = speed limiter disabled';
        $config[] = 'wrong_way_limiter_disabled='.(isset($this->serverConfig['wrongWayLimiterDisabled']) ? ($this->serverConfig['wrongWayLimiterDisabled'] ? '1' : '0') : '0');
        $config[] = '';

        // Weather
        $config[] = '# Set event weather, list available weather names with command: weathers';
        $config[] = '# Leave blank for random weather';
        $config[] = 'weather='.($this->serverConfig['weather'] ?? '');
        $config[] = '';

        // Frequency
        $config[] = '# Set server update frequency to low or high';
        $config[] = 'frequency='.($this->serverConfig['frequency'] ?? 'high');
        $config[] = '';

        // Mods
        $config[] = '# Enable mod(s) on the server, mod folder names in a comma separated list';
        $config[] = '# Please note that you need to copy the folder of each mod';
        $config[] = "# to the mods folder in the server's installation location";
        if (isset($this->serverConfig['mods']) && ! empty($this->serverConfig['mods'])) {
            $config[] = 'mods='.$this->serverConfig['mods'];
        } else {
            $config[] = '#mods=example,my_mod';
        }
        $config[] = '';

        // Log
        $config[] = '# Save server console output to a log file';
        $config[] = '# To disable logging leave the filename blank ("log=") but do not comment out the line';
        $config[] = 'log='.($this->serverConfig['log'] ?? '');
        $config[] = '';

        // Event Loop (track rotation)
        if (! empty($this->trackRotation)) {
            $config[] = '# Event Loop (el) settings';
            $config[] = '#-------------------------------------------------------------------------------';
            $config[] = '#  If enabled, server will automatically rotate events as configured below';
            $config[] = '#  Each "el_add" setting signifies a new event and you can add as many as you wish';
            $config[] = '#  The other settings can used to override corresponding global settings configured above';
            $config[] = '';

            foreach ($this->trackRotation as $index => $track) {
                if (isset($track['track'])) {
                    $config[] = 'el_add='.$track['track'];
                }
                if (isset($track['gamemode']) && ! empty($track['gamemode'])) {
                    $config[] = 'el_gamemode='.$track['gamemode'];
                }
                if (isset($track['laps']) && ! empty($track['laps'])) {
                    $config[] = 'el_laps='.$track['laps'];
                }
                if (isset($track['timeLimit']) && ! empty($track['timeLimit'])) {
                    $config[] = 'el_time_limit='.$track['timeLimit'];
                }
                if (isset($track['bots']) && ! empty($track['bots'])) {
                    $config[] = 'el_bots='.$track['bots'];
                }
                if (isset($track['weather']) && ! empty($track['weather'])) {
                    $config[] = 'el_weather='.$track['weather'];
                }
                if (isset($track['carClassRestriction']) && ! empty($track['carClassRestriction'])) {
                    $config[] = 'el_car_class_restriction='.$track['carClassRestriction'];
                }
                if (isset($track['carRestriction']) && ! empty($track['carRestriction'])) {
                    $config[] = 'el_car_restriction='.$track['carRestriction'];
                }
                $config[] = '';
            }
        }

        $this->plainTextConfig = implode("\n", $config);
    }

    protected function getViewData(): array
    {
        return [
            'serverConfig' => $this->serverConfig,
            'trackRotation' => $this->trackRotation,
            'plainTextConfig' => $this->plainTextConfig,
        ];
    }
}
