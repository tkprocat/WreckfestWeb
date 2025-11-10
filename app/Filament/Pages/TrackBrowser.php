<?php

namespace App\Filament\Pages;

use App\Models\Tag;
use App\Models\TrackVariant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;

class TrackBrowser extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected string $view = 'filament.pages.track-browser';

    protected static ?string $navigationLabel = 'Track Browser';

    protected static ?string $title = 'Track Browser';

    public ?string $search = '';

    public ?string $gamemode = '';

    public ?string $weather = '';

    public ?string $derbyFilter = '';

    public array $selectedTags = [];

    public ?string $sortColumn = 'location';

    public ?string $sortDirection = 'asc';

    public int $visibleCount = 12;

    public bool $resultsLoaded = false;

    public function updatedSearch(): void
    {
        $this->visibleCount = 12;
    }

    public function updatedGamemode(): void
    {
        $this->visibleCount = 12;
    }

    public function updatedWeather(): void
    {
        $this->visibleCount = 12;
    }

    public function updatedDerbyFilter(): void
    {
        $this->visibleCount = 12;
    }

    public function updatedSelectedTags(): void
    {
        $this->visibleCount = 12;
    }

    public function loadResults(): void
    {
        $this->resultsLoaded = true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('search')
                    ->label('Search Tracks')
                    ->placeholder('Search by track name or variant (e.g., "Oval", "Demolition Arena")')
                    ->live(debounce: 500)
                    ->columnSpan(2),

                Select::make('gamemode')
                    ->label('Game Mode')
                    ->options([
                        '' => 'All Game Modes',
                        ...config('wreckfest.gamemodes', []),
                    ])
                    ->native(false)
                    ->live(),

                Select::make('derbyFilter')
                    ->label('Track Type')
                    ->options([
                        '' => 'All Types',
                        'derby' => 'Derby Only',
                        'racing' => 'Racing Only',
                    ])
                    ->native(false)
                    ->live(),

                Select::make('weather')
                    ->label('Weather Support')
                    ->options([
                        '' => 'Any Weather',
                        ...config('wreckfest.weather_conditions', []),
                    ])
                    ->native(false)
                    ->live(),

                Select::make('selectedTags')
                    ->label('Filter by Tags')
                    ->multiple()
                    ->options(fn () => $this->availableTags->pluck('name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->placeholder('Select tags to filter...')
                    ->live()
                    ->columnSpan(2),
            ])
            ->columns(4);
    }

    public function getTracksProperty(): Collection
    {
        if (! $this->resultsLoaded) {
            return collect();
        }

        return $this->getFilteredTracks();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            // Toggle direction if clicking the same column
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // New column, default to ascending
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function loadMore(): void
    {
        $this->visibleCount += 12;
    }

    public function getVisibleTracksProperty(): Collection
    {
        $visibleTracks = $this->tracks->take($this->visibleCount);

        // Now load tags ONLY for the visible tracks
        $variantIds = $visibleTracks->pluck('id')->filter()->unique();

        if ($variantIds->isNotEmpty()) {
            // Single query to load all tags for visible variants
            $variantsWithTags = TrackVariant::with('tags')
                ->whereIn('id', $variantIds->toArray())
                ->get()
                ->keyBy('id');

            // Attach tags to the visible track objects
            foreach ($visibleTracks as $track) {
                if (isset($variantsWithTags[$track->id])) {
                    $track->tags = $variantsWithTags[$track->id]->tags;
                }
            }
        }

        return $visibleTracks;
    }

    protected function getFilteredTracks(): Collection
    {
        // Build query with filters applied at database level
        // Only eager load 'track', not 'tags' (tags will be loaded separately for visible tracks)
        $query = TrackVariant::with(['track']);

        // Search filter - apply at database level
        if (! empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('variant_id', 'like', "%{$search}%")
                    ->orWhereHas('track', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // Derby filter
        if (! empty($this->derbyFilter)) {
            if ($this->derbyFilter === 'derby') {
                $query->where('game_mode', 'Derby');
            } elseif ($this->derbyFilter === 'racing') {
                $query->where('game_mode', '!=', 'Derby');
            }
        }

        // Tag filter - must have ALL selected tags
        if (! empty($this->selectedTags)) {
            foreach ($this->selectedTags as $tagId) {
                $query->whereHas('tags', fn($q) => $q->where('tags.id', $tagId));
            }
        }

        // Weather filter - apply at database level using JSON
        if (! empty($this->weather) && $this->weather !== 'random') {
            $weather = $this->weather;
            $query->where(function ($q) use ($weather) {
                $q->whereRaw("json_array_length(weather_conditions) = 0")
                    ->orWhereRaw("EXISTS (SELECT 1 FROM json_each(weather_conditions) WHERE value = ?)", [$weather]);
            });
        }

        // Apply sorting at database level
        switch ($this->sortColumn) {
            case 'variant':
                $query->orderBy('name', $this->sortDirection);
                break;
            case 'variant_id':
                $query->orderBy('variant_id', $this->sortDirection);
                break;
            case 'derby':
                $query->orderBy('game_mode', $this->sortDirection);
                break;
            case 'location':
            default:
                $query->join('tracks', 'track_variants.track_id', '=', 'tracks.id')
                    ->orderBy('tracks.name', $this->sortDirection)
                    ->select('track_variants.*');
                break;
        }

        // Get filtered variants
        $variants = $query->get();

        // Get config values once, outside the loop (optimization)
        $allWeatherKeys = array_keys(config('wreckfest.weather_conditions', []));
        $derbyModes = config('wreckfest.derby_gamemodes', []);
        $racingModes = config('wreckfest.racing_gamemodes', []);
        $gamemodeLabels = config('wreckfest.gamemodes', []);

        // Map to track objects (now only processing filtered results)
        $result = $variants->map(function ($variant) use ($allWeatherKeys, $derbyModes, $racingModes, $gamemodeLabels) {
            $supportedWeather = $variant->weather_conditions ?? $allWeatherKeys;
            $compatibleGamemodes = $variant->game_mode === 'Derby' ? $derbyModes : $racingModes;

            // Map gamemode keys to labels
            $gamemodeLabelsForTrack = collect($compatibleGamemodes)->mapWithKeys(function ($mode) use ($gamemodeLabels) {
                return [$mode => $gamemodeLabels[$mode] ?? ucfirst($mode)];
            })->toArray();

            return (object) [
                'id' => $variant->id,
                'location' => $variant->track->name,
                'variant' => $variant->name,
                'variant_id' => $variant->variant_id,
                'derby' => $variant->game_mode === 'Derby',
                'weather' => $supportedWeather,
                'compatible_gamemodes' => array_keys($gamemodeLabelsForTrack),
                'gamemode_labels' => $gamemodeLabelsForTrack,
                'tags' => null, // Will be loaded separately for visible tracks only
            ];
        })->filter(function ($track) {
            // Only weather and gamemode filters remain in PHP (they're complex)
            return $this->shouldIncludeTrack($track);
        })->values();

        return $result;
    }

    protected function shouldIncludeTrack($track): bool
    {
        // Game mode filter (complex logic with config arrays, kept in PHP)
        if (! empty($this->gamemode)) {
            if (! in_array($this->gamemode, $track->compatible_gamemodes)) {
                return false;
            }
        }

        return true;
    }

    public function updateTags(int $variantId, array $tagIds): void
    {
        $variant = TrackVariant::findOrFail($variantId);
        $variant->tags()->sync($tagIds);

        Notification::make()
            ->title('Tags updated')
            ->body("Updated tags for {$variant->full_name}")
            ->success()
            ->send();
    }

    public function getAvailableTagsProperty(): Collection
    {
        return Tag::orderBy('name')->get();
    }
}
