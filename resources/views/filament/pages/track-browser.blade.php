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
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium" style="background-color: {{ config('wreckfest.brand.primary') }}20; color: {{ config('wreckfest.brand.primary') }};">
                                                {{ ucfirst($weather) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($track->compatible_gamemodes as $gamemode)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300">
                                                {{ config("wreckfest.gamemodes.$gamemode", ucfirst($gamemode)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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
        </x-filament::section>
    </div>
</x-filament-panels::page>
