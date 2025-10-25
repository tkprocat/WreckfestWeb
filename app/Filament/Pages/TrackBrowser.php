<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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

    public ?string $sortColumn = 'location';

    public ?string $sortDirection = 'asc';

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
            ])
            ->columns(4);
    }

    public function getTracksProperty(): Collection
    {
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

    protected function getFilteredTracks(): Collection
    {
        $trackLocations = config('wreckfest.tracks', []);
        $allTracks = collect();

        foreach ($trackLocations as $locationKey => $location) {
            $locationName = $location['name'] ?? $locationKey;
            $variants = $location['variants'] ?? [];
            $locationWeather = $location['weather'] ?? null;

            foreach ($variants as $variantId => $variant) {
                $variantName = is_array($variant) ? ($variant['name'] ?? $variantId) : $variant;
                $isDerby = is_array($variant) ? ($variant['derby'] ?? false) : false;

                // Get supported weather for this variant
                $supportedWeather = $locationWeather ?? array_keys(config('wreckfest.weather_conditions', []));

                // Get compatible game modes
                $compatibleGamemodes = $isDerby
                    ? config('wreckfest.derby_gamemodes', [])
                    : config('wreckfest.racing_gamemodes', []);

                $track = (object) [
                    'location' => $locationName,
                    'variant' => $variantName,
                    'variant_id' => $variantId,
                    'derby' => $isDerby,
                    'weather' => $supportedWeather,
                    'compatible_gamemodes' => $compatibleGamemodes,
                ];

                // Apply filters
                if ($this->shouldIncludeTrack($track)) {
                    $allTracks->push($track);
                }
            }
        }

        // Apply sorting
        $allTracks = $allTracks->sortBy(function ($track) {
            return match ($this->sortColumn) {
                'location' => $track->location,
                'variant' => $track->variant,
                'variant_id' => $track->variant_id,
                'derby' => $track->derby,
                default => $track->location,
            };
        }, SORT_NATURAL | SORT_FLAG_CASE);

        if ($this->sortDirection === 'desc') {
            $allTracks = $allTracks->reverse();
        }

        return $allTracks->values();
    }

    protected function shouldIncludeTrack($track): bool
    {
        // Search filter
        if (! empty($this->search)) {
            $searchLower = strtolower($this->search);
            $locationMatch = str_contains(strtolower($track->location), $searchLower);
            $variantMatch = str_contains(strtolower($track->variant), $searchLower);
            $variantIdMatch = str_contains(strtolower($track->variant_id), $searchLower);

            if (! $locationMatch && ! $variantMatch && ! $variantIdMatch) {
                return false;
            }
        }

        // Game mode filter
        if (! empty($this->gamemode)) {
            if (! in_array($this->gamemode, $track->compatible_gamemodes)) {
                return false;
            }
        }

        // Derby filter
        if (! empty($this->derbyFilter)) {
            if ($this->derbyFilter === 'derby' && ! $track->derby) {
                return false;
            }
            if ($this->derbyFilter === 'racing' && $track->derby) {
                return false;
            }
        }

        // Weather filter
        if (! empty($this->weather) && $this->weather !== 'random') {
            if (! in_array($this->weather, $track->weather)) {
                return false;
            }
        }

        return true;
    }
}
