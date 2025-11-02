<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                Search & Filter
            </x-slot>

            <x-slot name="description">
                Find tracks by name, variant type, game mode, or weather support.
            </x-slot>

            {{ $this->form }}
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Track Results ({{ $this->tracks->count() }} tracks found)
            </x-slot>

            <!-- Grid View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @forelse($this->tracks as $track)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Track Image -->
                        <div class="aspect-video bg-gray-100 dark:bg-gray-900 relative overflow-hidden pt-4">
                            @php
                                $imageFilename = str_replace(['/', ' '], ['_', '_'], strtolower($track->variant_id)) . '.png';
                                $imagePath = asset("images/tracks/{$imageFilename}");
                            @endphp

                            <img
                                src="{{ $imagePath }}"
                                alt="{{ $track->variant }}"
                                class="w-full h-full object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                            >
                            <div class="w-full h-full flex items-center justify-center" style="display: none;">
                                <div class="text-center">
                                    <x-filament::icon icon="heroicon-o-photo" class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-2" />
                                    <p class="text-xs text-gray-500 dark:text-gray-400">No preview available</p>
                                </div>
                            </div>

                            <!-- Derby Badge -->
                            @if($track->derby)
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-lg">
                                        DERBY
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Track Info -->
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-1">{{ $track->location }}</h3>
                            <p class="text-sm font-medium mb-2" style="color: {{ config('wreckfest.brand.primary') }};">
                                {{ $track->variant }}
                            </p>

                            <div class="flex items-center gap-2 mb-3">
                                <code class="text-xs bg-gray-100 dark:bg-gray-900 px-2 py-1 rounded font-mono text-gray-600 dark:text-gray-400">
                                    {{ $track->variant_id }}
                                </code>
                                <button
                                    onclick="navigator.clipboard.writeText('{{ $track->variant_id }}'); this.innerHTML = '<svg class=\'w-3 h-3\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M9 2a1 1 0 000 2h2a1 1 0 100-2H9z\'/><path d=\'M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z\'/></svg> Copied!'; setTimeout(() => this.innerHTML = '<svg class=\'w-3 h-3\' fill=\'currentColor\' viewBox=\'0 0 20 20\'><path d=\'M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z\'/><path d=\'M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z\'/></svg>', 2000);"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"
                                    title="Copy variant ID"
                                >
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                                        <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Weather -->
                            <div class="mb-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Weather:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($track->weather as $weather)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200">
                                            {{ ucfirst($weather) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Game Modes -->
                            <div class="mb-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Game Modes:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($track->compatible_gamemodes as $gamemode)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                            {{ config("wreckfest.gamemodes.$gamemode", ucfirst($gamemode)) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Tags -->
                            <div x-data="{ editing: false, selectedTags: {{ json_encode($track->tags->pluck('id')->toArray()) }} }">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Tags:</p>
                                    <button
                                        @click="editing = !editing"
                                        class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
                                        type="button"
                                    >
                                        <span x-show="!editing">Edit</span>
                                        <span x-show="editing">Done</span>
                                    </button>
                                </div>

                                <!-- Display Mode -->
                                <div x-show="!editing" class="flex flex-wrap gap-1 min-h-[24px]">
                                    @forelse($track->tags as $tag)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200"
                                              style="background-color: {{ $tag->color ? $tag->color.'20' : '' }}; color: {{ $tag->color ?? '' }};">
                                            {{ $tag->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 dark:text-gray-600 italic">No tags</span>
                                    @endforelse
                                </div>

                                <!-- Edit Mode -->
                                <div x-show="editing" class="space-y-2">
                                    <div class="max-h-32 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 space-y-1">
                                        @foreach($this->availableTags as $tag)
                                            <label class="flex items-center gap-2 text-xs cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 p-1 rounded">
                                                <input
                                                    type="checkbox"
                                                    value="{{ $tag->id }}"
                                                    x-model="selectedTags"
                                                    class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                                                >
                                                <span class="text-gray-700 dark:text-gray-300">{{ $tag->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <button
                                        @click="$wire.updateTags({{ $track->id }}, selectedTags); editing = false;"
                                        class="w-full px-3 py-1.5 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md"
                                        type="button"
                                    >
                                        Save Tags
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center space-y-2">
                            <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-12 h-12 text-gray-400" />
                            <p class="text-sm">No tracks found matching your criteria</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Table View (collapsed by default) -->
            <details class="mt-6">
                <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 mb-4">
                    Show detailed table view
                </summary>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <button wire:click="sortBy('location')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                    Track Location
                                    @if($sortColumn === 'location')
                                        @if($sortDirection === 'asc')
                                            <x-filament::icon icon="heroicon-m-chevron-up" class="w-4 h-4" />
                                        @else
                                            <x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4" />
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <button wire:click="sortBy('variant')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                    Variant
                                    @if($sortColumn === 'variant')
                                        @if($sortDirection === 'asc')
                                            <x-filament::icon icon="heroicon-m-chevron-up" class="w-4 h-4" />
                                        @else
                                            <x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4" />
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <button wire:click="sortBy('variant_id')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                    Variant ID
                                    @if($sortColumn === 'variant_id')
                                        @if($sortDirection === 'asc')
                                            <x-filament::icon icon="heroicon-m-chevron-up" class="w-4 h-4" />
                                        @else
                                            <x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4" />
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <button wire:click="sortBy('derby')" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200 mx-auto">
                                    Derby
                                    @if($sortColumn === 'derby')
                                        @if($sortDirection === 'asc')
                                            <x-filament::icon icon="heroicon-m-chevron-up" class="w-4 h-4" />
                                        @else
                                            <x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4" />
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Supported Weather
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Compatible Game Modes
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Tags
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($this->tracks as $track)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $track->location }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: {{ config('wreckfest.brand.primary') }};">
                                    {{ $track->variant }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    <button
                                        onclick="navigator.clipboard.writeText('{{ $track->variant_id }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = '{{ $track->variant_id }}', 2000);"
                                        class="hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer"
                                        title="Click to copy"
                                    >
                                        {{ $track->variant_id }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if($track->derby)
                                        <x-filament::icon
                                            icon="heroicon-o-check-circle"
                                            class="w-5 h-5 text-success-500 mx-auto"
                                        />
                                    @else
                                        <x-filament::icon
                                            icon="heroicon-o-x-circle"
                                            class="w-5 h-5 text-gray-400 dark:text-gray-600 mx-auto"
                                        />
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($track->weather as $weather)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200">
                                                {{ ucfirst($weather) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($track->compatible_gamemodes as $gamemode)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                                {{ config("wreckfest.gamemodes.$gamemode", ucfirst($gamemode)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div x-data="{ editing: false, selectedTags: {{ json_encode($track->tags->pluck('id')->toArray()) }} }">
                                        <!-- Display Mode -->
                                        <div x-show="!editing" class="flex flex-wrap gap-1 items-center min-h-[28px]">
                                            @forelse($track->tags as $tag)
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200"
                                                      style="background-color: {{ $tag->color ? $tag->color.'20' : '' }}; color: {{ $tag->color ?? '' }};">
                                                    {{ $tag->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-400 dark:text-gray-600 italic">No tags</span>
                                            @endforelse
                                            <button
                                                @click="editing = true"
                                                class="ml-2 text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
                                                type="button"
                                            >
                                                Edit
                                            </button>
                                        </div>

                                        <!-- Edit Mode -->
                                        <div x-show="editing" class="space-y-2">
                                            <div class="max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-2 space-y-1 bg-white dark:bg-gray-800">
                                                @foreach($this->availableTags as $tag)
                                                    <label class="flex items-center gap-2 text-xs cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-1 rounded">
                                                        <input
                                                            type="checkbox"
                                                            value="{{ $tag->id }}"
                                                            x-model="selectedTags"
                                                            class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500"
                                                        >
                                                        <span class="text-gray-700 dark:text-gray-300">{{ $tag->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <div class="flex gap-2">
                                                <button
                                                    @click="$wire.updateTags({{ $track->id }}, selectedTags); editing = false;"
                                                    class="px-3 py-1 text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-md"
                                                    type="button"
                                                >
                                                    Save
                                                </button>
                                                <button
                                                    @click="editing = false; selectedTags = {{ json_encode($track->tags->pluck('id')->toArray()) }}"
                                                    class="px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md"
                                                    type="button"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center space-y-2">
                                        <x-filament::icon
                                            icon="heroicon-o-magnifying-glass"
                                            class="w-12 h-12 text-gray-400"
                                        />
                                        <p class="text-sm">No tracks found matching your criteria</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </details>
        </x-filament::section>
    </div>
</x-filament-panels::page>
