<?php

namespace App\Filament\Pages;

use App\Exceptions\WreckfestApiException;
use App\Helpers\TrackHelper;
use App\Services\WreckfestApiClient;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ServerConfig extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Server Config';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.server-config';

    public ?array $data = [];

    public function mount(): void
    {
        try {
            $apiClient = app(WreckfestApiClient::class);
            $config = $apiClient->getServerConfig();

            $this->form->fill($config);
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to contact Wreckfest Controller')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->persistent()
                ->send();

            $this->form->fill([]);
        }
    }

    protected function getAllTracks(): array
    {
        $trackLocations = config('wreckfest.tracks', []);
        $allTracks = [];

        foreach ($trackLocations as $locationKey => $location) {
            $locationName = $location['name'] ?? $locationKey;
            $variants = $location['variants'] ?? [];

            foreach ($variants as $variantId => $variant) {
                $variantName = is_array($variant) ? ($variant['name'] ?? $variantId) : $variant;
                $allTracks[$variantId] = $locationName . ' - ' . $variantName;
            }
        }

        return $allTracks;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Server Information')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('serverName')
                                ->label('Server Name')
                                ->required(),
                            TextInput::make('maxPlayers')
                                ->label('Max Players')
                                ->numeric()
                                ->required(),
                        ]),
                        Textarea::make('welcomeMessage')
                            ->label('Welcome Message')
                            ->rows(3),
                        TextInput::make('password')
                            ->label('Server Password')
                            ->password()
                            ->revealable(),
                    ]),

                Section::make('Network Settings')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('steamPort')
                                ->label('Steam Port')
                                ->numeric()
                                ->required(),
                            TextInput::make('gamePort')
                                ->label('Game Port')
                                ->numeric()
                                ->required(),
                            TextInput::make('queryPort')
                                ->label('Query Port')
                                ->numeric()
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Toggle::make('lan')
                                ->label('LAN Mode')
                                ->inline(false),
                            Toggle::make('excludeFromQuickplay')
                                ->label('Exclude From Quickplay')
                                ->inline(false),
                        ]),
                    ]),

                Section::make('Game Settings')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('sessionMode')
                                ->label('Session Mode')
                                ->options(config('wreckfest.session_modes'))
                                ->searchable()
                                ->placeholder('Select session mode'),
                            Select::make('gridOrder')
                                ->label('Grid Order')
                                ->options(config('wreckfest.grid_orders'))
                                ->searchable()
                                ->placeholder('Select grid order'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('lobbyCountdown')
                                ->label('Lobby Countdown')
                                ->numeric(),
                            TextInput::make('readyPlayersRequired')
                                ->label('Ready Players Required')
                                ->numeric(),
                            TextInput::make('bots')
                                ->label('Number of Bots')
                                ->numeric(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('aiDifficulty')
                                ->label('AI Difficulty')
                                ->options(config('wreckfest.ai_difficulties'))
                                ->searchable()
                                ->placeholder('Select AI difficulty'),
                            Select::make('vehicleDamage')
                                ->label('Vehicle Damage')
                                ->options(config('wreckfest.vehicle_damages'))
                                ->searchable()
                                ->placeholder('Select vehicle damage'),
                        ]),
                    ]),

                Section::make('Track & Race Settings')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('track')
                                ->label('Default Track')
                                ->options(fn () => $this->getAllTracks())
                                ->searchable()
                                ->placeholder('Select a track'),
                            Select::make('gamemode')
                                ->label('Default Game Mode')
                                ->options(config('wreckfest.gamemodes'))
                                ->searchable()
                                ->placeholder('Select game mode'),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('laps')
                                ->label('Laps')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(99),
                            TextInput::make('timeLimit')
                                ->label('Time Limit (minutes)')
                                ->numeric()
                                ->minValue(0),
                            TextInput::make('eliminationInterval')
                                ->label('Elimination Interval (seconds)')
                                ->numeric()
                                ->minValue(0),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('carClassRestriction')
                                ->label('Car Class Restriction')
                                ->options(config('wreckfest.car_classes'))
                                ->searchable()
                                ->placeholder('No restriction'),
                            Select::make('carRestriction')
                                ->label('Specific Car Restriction')
                                ->options(config('wreckfest.cars'))
                                ->searchable()
                                ->placeholder('Leave empty for no restriction'),
                        ]),
                        Select::make('weather')
                            ->label('Weather Condition')
                            ->options(config('wreckfest.weather_conditions'))
                            ->searchable()
                            ->placeholder('Random'),
                    ]),

                Section::make('Advanced Settings')
                    ->schema([
                        Grid::make(3)->schema([
                            Toggle::make('enableTrackVote')
                                ->label('Enable Track Vote')
                                ->inline(false),
                            Toggle::make('disableIdleKick')
                                ->label('Disable Idle Kick')
                                ->inline(false),
                            Toggle::make('specialVehiclesDisabled')
                                ->label('Disable Special Vehicles')
                                ->inline(false),
                        ]),
                        Grid::make(3)->schema([
                            Toggle::make('carResetDisabled')
                                ->label('Disable Car Reset')
                                ->inline(false),
                            Toggle::make('wrongWayLimiterDisabled')
                                ->label('Disable Wrong Way Limiter')
                                ->inline(false),
                            Toggle::make('clearUsers')
                                ->label('Clear Users')
                                ->inline(false),
                        ]),
                        Grid::make(3)->schema([
                            Toggle::make('ownerDisabled')
                                ->label('Disable Owner')
                                ->inline(false),
                            Toggle::make('adminControl')
                                ->label('Admin Control')
                                ->inline(false),
                            TextInput::make('carResetDelay')
                                ->label('Car Reset Delay')
                                ->numeric(),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('numTeams')
                                ->label('Number of Teams')
                                ->numeric(),
                            Select::make('frequency')
                                ->label('Server Update Frequency')
                                ->options(config('wreckfest.frequencies'))
                                ->searchable()
                                ->placeholder('Select frequency'),
                            TextInput::make('log')
                                ->label('Log File Path')
                                ->placeholder('log.txt'),
                        ]),
                        Textarea::make('adminSteamIds')
                            ->label('Admin Steam IDs (comma separated)')
                            ->rows(2),
                        Textarea::make('opSteamIds')
                            ->label('OP Steam IDs (comma separated)')
                            ->rows(2),
                        Textarea::make('mods')
                            ->label('Mods (comma separated)')
                            ->rows(2),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Convert toggle values to integers (API expects 0/1)
        $booleanFields = [
            'lan', 'excludeFromQuickplay', 'enableTrackVote',
            'disableIdleKick', 'specialVehiclesDisabled', 'carResetDisabled',
            'wrongWayLimiterDisabled', 'clearUsers', 'ownerDisabled', 'adminControl'
        ];

        foreach ($booleanFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = $data[$field] ? 1 : 0;
            }
        }

        $apiClient = app(WreckfestApiClient::class);

        if ($apiClient->updateServerConfig($data)) {
            Notification::make()
                ->title('Configuration saved successfully')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to save configuration')
                ->danger()
                ->send();
        }
    }
}
