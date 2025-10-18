<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold">Current Players</h3>
                <x-filament::button wire:click="refreshPlayers" color="gray" size="sm">
                    Refresh
                </x-filament::button>
            </div>

            @if(is_array($players) && count($players) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($players as $player)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="px-4 py-2" {!! $this->isBot($player) ? 'style="color: ' . config('wreckfest.brand.primary') . ';"' : '' !!}>
                                    {{ $this->formatPlayerName($player) }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Total Players: {{ count($players) }}
                </div>
            @else
                <div class="text-center py-4">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No players are currently online.</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Check back later or start the server to see players connect.</p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
