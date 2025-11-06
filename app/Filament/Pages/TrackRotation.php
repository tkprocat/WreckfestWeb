<?php

namespace App\Filament\Pages;

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
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Attributes\On;

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

    public function loadCollectionById(int $id, bool $skipNotification = false): void
    {
        $collection = TrackCollection::find($id);

        if ($collection) {
            $this->currentCollectionId = $collection->id;
            $this->currentCollectionName = $collection->name;
            session(['track_rotation.current_collection_id' => $collection->id]);

            // Update the track list
            $this->updateTrackList($collection->tracks);

            // Only send notification if not skipped (to avoid duplicates from AI widget)
            if (!$skipNotification) {
                Notification::make()
                    ->title('Collection loaded')
                    ->body("Working on: {$collection->name}")
                    ->success()
                    ->send();
            }
        }
    }

    /**
     * Update the track list and force a re-render
     */
    public function updateTrackList(array $tracks): void
    {
        // Directly update the data property that the form is bound to
        $this->data['tracks'] = $tracks;

        // Also fill the form to ensure Filament's state is synced
        $this->form->fill(['tracks' => $tracks]);

        // Dispatch event for frontend listeners
        $this->dispatch('collection-loaded', tracks: $tracks);
    }

    #[On('refresh-track-rotation')]
    public function refreshFromAI(): void
    {
        // Reload the current collection if one is selected
        if ($this->currentCollectionId) {
            $this->loadCollectionById($this->currentCollectionId);
        }
    }

    #[On('load-collection')]
    public function loadCollectionFromAI(int $collectionId): void
    {
        logger()->info('TrackRotation: Received load-collection event', [
            'collection_id' => $collectionId,
            'current_id' => $this->currentCollectionId
        ]);

        $this->loadCollectionById($collectionId, $skipNotification = true);

        logger()->info('TrackRotation: After loadCollectionById', [
            'new_current_id' => $this->currentCollectionId,
            'data_tracks_count' => count($this->data['tracks'] ?? [])
        ]);
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
                ->body(count($tracks).' tracks loaded')
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

            // Get the current collection name from the server
            $serverCollectionName = $apiClient->getTrackCollectionName();

            // Try to find a matching collection in database by name
            if ($serverCollectionName) {
                $collection = TrackCollection::where('name', $serverCollectionName)->first();

                if ($collection) {
                    // Found matching collection - set it as current
                    $this->currentCollectionId = $collection->id;
                    $this->currentCollectionName = $collection->name;
                    session(['track_rotation.current_collection_id' => $collection->id]);

                    $this->form->fill(['tracks' => $collection->tracks]);
                    $this->dispatch('collection-loaded', tracks: $collection->tracks);

                    Notification::make()
                        ->title('Active collection loaded')
                        ->body("Working on: {$collection->name} (from server)")
                        ->success()
                        ->send();

                    return;
                }
            }

            // Try to load the last worked-on collection from session (fallback)
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

            // Otherwise load tracks directly from server
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
        $variants = \App\Models\TrackVariant::with('track')->get();
        $allTracks = [];

        foreach ($variants as $variant) {
            $allTracks[$variant->variant_id] = $variant->full_name;
        }

        return $allTracks;
    }

    protected function searchTracksByTag(string $search): array
    {
        // Search for tracks by tag name
        $variants = \App\Models\TrackVariant::with(['track', 'tags'])
            ->whereHas('tags', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();

        $results = [];
        foreach ($variants as $variant) {
            $results[$variant->variant_id] = $variant->full_name;
        }

        return $results;
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

                                    // Search by track name
                                    $tracks = $this->getAllTracks();
                                    $nameResults = array_filter($tracks, function ($label) use ($search) {
                                        return str_contains(strtolower($label), strtolower($search));
                                    }, ARRAY_FILTER_USE_BOTH);

                                    // Search by tag
                                    $tagResults = $this->searchTracksByTag($search);

                                    // Merge results (array_merge will combine and keep unique keys)
                                    return $nameResults + $tagResults;
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
                        if (! isset($state['track'])) {
                            return 'New Track';
                        }

                        $tracks = $this->getAllTracks();
                        $trackName = $tracks[$state['track']] ?? $state['track'];
                        $gamemodeName = config('wreckfest.gamemodes.'.($state['gamemode'] ?? $this->defaultGamemode), ucfirst($state['gamemode'] ?? $this->defaultGamemode));
                        $lapsText = isset($state['laps']) ? ' - '.$state['laps'].' laps' : '';

                        return $trackName.' ('.$gamemodeName.$lapsText.')';
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

                                    if (! empty($tracks)) {
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

            // Use current collection name or default to "Manual"
            $collectionName = $this->currentCollectionName ?? 'Manual';

            if ($apiClient->updateTracks($tracks, $collectionName)) {
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
                            ->body(count($tracks).' tracks loaded')
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
                            ->placeholder('e.g., Copy of '.($this->currentCollectionName ?? 'Collection'))
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
