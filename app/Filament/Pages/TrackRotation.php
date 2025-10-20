<?php

namespace App\Filament\Pages;

use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use App\Exceptions\WreckfestApiException;
use App\Models\TrackCollection;
use App\Services\WreckfestApiClient;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TrackRotation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Track Rotation';
    protected static ?int $navigationSort = 2;
    protected string $view = 'filament.pages.track-rotation';

    public ?array $data = [];
    public ?string $defaultGamemode = null;
    public ?int $currentCollectionId = null;
    public ?string $currentCollectionName = null;

    public function updatedCurrentCollectionId($value): void
    {
        if ($value === null || $value === '') {
            // User selected "Loaded from server"
            $this->loadFromServer();
            return;
        }

        $this->loadCollectionById($value);
    }

    public function loadCollectionById(int $id): void
    {
        $collection = TrackCollection::find($id);

        if ($collection) {
            $this->currentCollectionId = $collection->id;
            $this->currentCollectionName = $collection->name;
            session(['track_rotation.current_collection_id' => $collection->id]);

            $this->form->fill(['tracks' => $collection->tracks]);

            $this->dispatch('collection-loaded', tracks: $collection->tracks);

            Notification::make()
                ->title('Collection loaded')
                ->body("Working on: {$collection->name}")
                ->success()
                ->send();
        }
    }

    public function loadFromServer(): void
    {
        try {
            $apiClient = app(WreckfestApiClient::class);
            $tracks = $apiClient->getTracks();
            $serverConfig = $apiClient->getServerConfig();

            // Update the default gamemode from server config
            $this->defaultGamemode = $serverConfig['gamemode'] ?? 'racing';

            // Clear current collection context
            $this->currentCollectionId = null;
            $this->currentCollectionName = null;
            session()->forget('track_rotation.current_collection_id');

            $this->form->fill(['tracks' => $tracks]);

            $this->dispatch('collection-loaded', tracks: $tracks);

            Notification::make()
                ->title('Loaded from server')
                ->body(count($tracks) . ' tracks loaded')
                ->success()
                ->send();
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to contact Wreckfest Controller')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->send();
        }
    }

    public function mount(): void
    {
        try {
            $apiClient = app(WreckfestApiClient::class);
            $serverConfig = $apiClient->getServerConfig();

            // Store the default gamemode from server config
            $this->defaultGamemode = $serverConfig['gamemode'] ?? 'racing';

            // Try to load the last worked-on collection from session
            $lastCollectionId = session('track_rotation.current_collection_id');

            if ($lastCollectionId) {
                $collection = TrackCollection::find($lastCollectionId);
                if ($collection) {
                    $this->currentCollectionId = $collection->id;
                    $this->currentCollectionName = $collection->name;
                    $this->form->fill(['tracks' => $collection->tracks]);
                    $this->dispatch('collection-loaded', tracks: $collection->tracks);
                    return;
                }
            }

            // Otherwise load from server
            $tracks = $apiClient->getTracks();
            $this->form->fill(['tracks' => $tracks]);
            $this->dispatch('collection-loaded', tracks: $tracks);
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to contact Wreckfest Controller')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->persistent()
                ->send();

            $this->defaultGamemode = 'racing';
            $this->form->fill(['tracks' => []]);
            $this->dispatch('collection-loaded', tracks: []);
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

        // Filter out any null or empty values to satisfy Filament 4's stricter Select validation
        return array_filter($allTracks, fn($value) => is_string($value) && $value !== '');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('tracks')
                    ->label('Track Rotation')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('track')
                                ->label('Track')
                                ->options(fn () => $this->getAllTracks())
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search): array {
                                    if (empty($search)) {
                                        return [];
                                    }

                                    $tracks = $this->getAllTracks();

                                    return array_filter($tracks, function ($label) use ($search) {
                                        return str_contains(strtolower($label), strtolower($search));
                                    }, ARRAY_FILTER_USE_BOTH);
                                })
                                ->required()
                                ->native(false),
                            Select::make('gamemode')
                                ->label('Game Mode')
                                ->options(config('wreckfest.gamemodes'))
                                ->searchable()
                                ->native(false),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('laps')
                                ->label('Laps')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(99)
                                ->default(5),
                            TextInput::make('bots')
                                ->label('Number of Bots')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(23)
                                ->default(0),
                            TextInput::make('numTeams')
                                ->label('Number of Teams')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(8),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('carClassRestriction')
                                ->label('Car Class Restriction')
                                ->options(config('wreckfest.car_classes'))
                                ->searchable()
                                ->native(false),
                            Select::make('carRestriction')
                                ->label('Specific Car Restriction')
                                ->options(config('wreckfest.cars'))
                                ->searchable()
                                ->native(false),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('weather')
                                ->label('Weather Condition')
                                ->options(config('wreckfest.weather_conditions'))
                                ->searchable()
                                ->native(false),
                            Toggle::make('carResetDisabled')
                                ->label('Disable Car Reset')
                                ->inline(false),
                        ]),
                        Toggle::make('wrongWayLimiterDisabled')
                            ->label('Disable Wrong Way Limiter')
                            ->inline(false),
                    ])
                    ->reorderable()
                    ->collapsible()
                    ->collapsed(true)
                    ->itemLabel(function (array $state): ?string {
                        if (!isset($state['track'])) {
                            return 'New Track';
                        }

                        $tracks = $this->getAllTracks();
                        $trackName = $tracks[$state['track']] ?? $state['track'];
                        $gamemodeName = config('wreckfest.gamemodes.' . ($state['gamemode'] ?? $this->defaultGamemode), ucfirst($state['gamemode'] ?? $this->defaultGamemode));
                        $lapsText = isset($state['laps']) ? ' - ' . $state['laps'] . ' laps' : '';

                        return $trackName . ' (' . $gamemodeName . $lapsText . ')';
                    })
                    ->addActionLabel('Add Track to Rotation')
                    ->defaultItems(0)
                    ->columns(1),
                Section::make()
                    ->schema([
                        Actions::make([
                            Action::make('randomizeOrder')
                        ->label('Randomize Order')
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->modalHeading('Randomize Track Order')
                        ->modalDescription('This will shuffle the current track rotation order. Are you sure?')
                        ->action(function (): void {
                            $formData = $this->form->getState();
                            $tracks = $formData['tracks'] ?? [];

                            if (!empty($tracks)) {
                                shuffle($tracks);
                                $this->form->fill(['tracks' => $tracks]);

                                // Don't update originalTracks - this is an unsaved change

                                Notification::make()
                                    ->title('Track order randomized')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->color('warning'),
                    Action::make('saveCollection')
                        ->label(fn () => $this->currentCollectionId ? 'Save Collection' : 'Save As New Collection')
                        ->icon('heroicon-o-bookmark')
                        ->schema(fn () => $this->currentCollectionId ? [] : [
                            TextInput::make('name')
                                ->label('Collection Name')
                                ->required()
                                ->placeholder('e.g., Racing Only, Mixed Modes, etc.')
                                ->maxLength(255),
                        ])
                        ->action(function (array $data): void {
                            $formData = $this->form->getState();
                            $tracks = $formData['tracks'] ?? [];

                            if ($this->currentCollectionId) {
                                // Update existing collection
                                $collection = TrackCollection::find($this->currentCollectionId);
                                if ($collection) {
                                    $collection->update(['tracks' => $tracks]);

                                    $this->dispatch('collection-saved', tracks: $tracks);

                                    Notification::make()
                                        ->title('Collection saved')
                                        ->body("Saved: {$collection->name}")
                                        ->success()
                                        ->send();
                                }
                            } else {
                                // Create new collection
                                $collection = TrackCollection::create([
                                    'name' => $data['name'],
                                    'tracks' => $tracks,
                                ]);

                                // Set as current collection
                                $this->currentCollectionId = $collection->id;
                                $this->currentCollectionName = $collection->name;
                                session(['track_rotation.current_collection_id' => $collection->id]);

                                $this->dispatch('collection-saved', tracks: $tracks);

                                Notification::make()
                                    ->title('Collection saved')
                                    ->body("Working on: {$collection->name}")
                                    ->success()
                                    ->send();
                            }
                        })
                        ->color('primary'),
                        ])
                        ->alignEnd()
                        ->fullWidth(),
                    ]),
            ])
            ->statePath('data');
    }

    public function deployToServer(): void
    {
        try {
            $data = $this->form->getState();
            $tracks = $data['tracks'] ?? [];

            // Convert toggle values to integers
            foreach ($tracks as &$track) {
                if (isset($track['carResetDisabled'])) {
                    $track['carResetDisabled'] = $track['carResetDisabled'] ? 1 : 0;
                }
                if (isset($track['wrongWayLimiterDisabled'])) {
                    $track['wrongWayLimiterDisabled'] = $track['wrongWayLimiterDisabled'] ? 1 : 0;
                }
            }

            $apiClient = app(WreckfestApiClient::class);

            if ($apiClient->updateTracks($tracks)) {
                Notification::make()
                    ->title('Track rotation deployed to server successfully')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Failed to deploy track rotation to server')
                    ->danger()
                    ->send();
            }
        } catch (WreckfestApiException $e) {
            Notification::make()
                ->title('Unable to contact Wreckfest Controller')
                ->body('Please ensure the Wreckfest API is running and accessible.')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deployToServer')
                ->label('Deploy to Server')
                ->icon('heroicon-o-arrow-up-tray')
                ->requiresConfirmation()
                ->modalHeading('Deploy Track Rotation to Server')
                ->modalDescription('This will update the track rotation on your game server. Are you sure?')
                ->action(fn () => $this->deployToServer())
                ->color('primary'),

            Action::make('refreshFromServer')
                ->label('Refresh from Server')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->modalHeading('Refresh from Server')
                ->modalDescription('This will load the current track rotation from your game server. Any unsaved changes will be lost.')
                ->action(function (): void {
                    try {
                        $apiClient = app(WreckfestApiClient::class);
                        $tracks = $apiClient->getTracks();
                        $serverConfig = $apiClient->getServerConfig();

                        // Update the default gamemode from server config
                        $this->defaultGamemode = $serverConfig['gamemode'] ?? 'racing';

                        // Clear current collection context
                        $this->currentCollectionId = null;
                        $this->currentCollectionName = null;
                        session()->forget('track_rotation.current_collection_id');

                        $this->form->fill(['tracks' => $tracks]);

                        $this->dispatch('collection-loaded', tracks: $tracks);

                        Notification::make()
                            ->title('Tracks refreshed from server')
                            ->body(count($tracks) . ' tracks loaded')
                            ->success()
                            ->send();
                    } catch (WreckfestApiException $e) {
                        Notification::make()
                            ->title('Unable to contact Wreckfest Controller')
                            ->body('Please ensure the Wreckfest API is running and accessible.')
                            ->danger()
                            ->send();
                    }
                })
                ->color('gray'),

            ActionGroup::make([
                Action::make('newCollection')
                ->label('New Collection')
                ->icon('heroicon-o-document-plus')
                ->schema([
                    TextInput::make('name')
                        ->label('Collection Name')
                        ->required()
                        ->placeholder('e.g., New Track Rotation')
                        ->maxLength(255)
                        ->default('New Collection'),
                ])
                ->action(function (array $data): void {
                    // Create a new empty collection
                    $collection = TrackCollection::create([
                        'name' => $data['name'],
                        'tracks' => [],
                    ]);

                    // Set as current collection
                    $this->currentCollectionId = $collection->id;
                    $this->currentCollectionName = $collection->name;
                    session(['track_rotation.current_collection_id' => $collection->id]);

                    // Clear the form
                    $this->form->fill(['tracks' => []]);

                    $this->dispatch('collection-loaded', tracks: []);

                    Notification::make()
                        ->title('New collection created')
                        ->body("Working on: {$collection->name}")
                        ->success()
                        ->send();
                })
                ->color('gray'),

            Action::make('loadCollection')
                ->label('Load Collection')
                ->icon('heroicon-o-folder-open')
                ->schema([
                    Select::make('collection_id')
                        ->label('Select Collection')
                        ->options(TrackCollection::pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    $collection = TrackCollection::find($data['collection_id']);

                    if ($collection) {
                        // Set as current collection
                        $this->currentCollectionId = $collection->id;
                        $this->currentCollectionName = $collection->name;
                        session(['track_rotation.current_collection_id' => $collection->id]);

                        $this->form->fill(['tracks' => $collection->tracks]);

                        Notification::make()
                            ->title('Collection loaded')
                            ->body("Working on: {$collection->name}")
                            ->success()
                            ->send();
                    }
                })
                ->color('info'),

            Action::make('saveCollection')
                ->label(fn () => $this->currentCollectionId ? 'Save Collection' : 'Save As New Collection')
                ->icon('heroicon-o-bookmark')
                ->schema(fn () => $this->currentCollectionId ? [] : [
                    TextInput::make('name')
                        ->label('Collection Name')
                        ->required()
                        ->placeholder('e.g., Racing Only, Mixed Modes, etc.')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $formData = $this->form->getState();
                    $tracks = $formData['tracks'] ?? [];

                    if ($this->currentCollectionId) {
                        // Update existing collection
                        $collection = TrackCollection::find($this->currentCollectionId);
                        if ($collection) {
                            $collection->update(['tracks' => $tracks]);

                            $this->dispatch('collection-saved', tracks: $tracks);

                            Notification::make()
                                ->title('Collection saved')
                                ->body("Saved: {$collection->name}")
                                ->success()
                                ->send();
                        }
                    } else {
                        // Create new collection
                        $collection = TrackCollection::create([
                            'name' => $data['name'],
                            'tracks' => $tracks,
                        ]);

                        // Set as current collection
                        $this->currentCollectionId = $collection->id;
                        $this->currentCollectionName = $collection->name;
                        session(['track_rotation.current_collection_id' => $collection->id]);

                        $this->dispatch('collection-saved', tracks: $tracks);

                        Notification::make()
                            ->title('Collection saved')
                            ->body("Working on: {$collection->name}")
                            ->success()
                            ->send();
                    }
                })
                ->color('primary'),

            Action::make('saveAsCollection')
                ->label('Save as new collection')
                ->icon('heroicon-o-document-duplicate')
                ->visible(fn () => $this->currentCollectionId !== null)
                ->schema([
                    TextInput::make('name')
                        ->label('New Collection Name')
                        ->required()
                        ->placeholder('e.g., Copy of ' . ($this->currentCollectionName ?? 'Collection'))
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $formData = $this->form->getState();
                    $tracks = $formData['tracks'] ?? [];

                    // Create new collection
                    $collection = TrackCollection::create([
                        'name' => $data['name'],
                        'tracks' => $tracks,
                    ]);

                    // Set as current collection
                    $this->currentCollectionId = $collection->id;
                    $this->currentCollectionName = $collection->name;
                    session(['track_rotation.current_collection_id' => $collection->id]);

                    $this->dispatch('collection-saved', tracks: $tracks);

                    Notification::make()
                        ->title('Collection saved as new')
                        ->body("Working on: {$collection->name}")
                        ->success()
                        ->send();
                })
                ->color('primary'),
            ])
                ->label('Collections')
                ->icon('heroicon-o-rectangle-stack')
                ->button()
                ->color('gray'),
        ];
    }
}
