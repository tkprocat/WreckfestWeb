@php
    $collections = \App\Models\TrackCollection::orderBy('created_at', 'desc')->get();
@endphp

<div class="space-y-4">
    @if($collections->isEmpty())
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No saved collections yet.</p>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Create a track rotation and save it as a collection to get started.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($collections as $collection)
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700" x-data="{ editing: false, name: '{{ $collection->name }}' }">
                    <div class="flex-1">
                        <div x-show="!editing">
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $collection->name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ count($collection->tracks) }} track{{ count($collection->tracks) !== 1 ? 's' : '' }}
                                • Created {{ $collection->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div x-show="editing" x-cloak>
                            <input
                                type="text"
                                x-model="name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                @keydown.enter="
                                    $wire.renameCollection({{ $collection->id }}, name);
                                    editing = false;
                                "
                                @keydown.escape="editing = false; name = '{{ $collection->name }}'"
                            />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Press Enter to save, Esc to cancel</p>
                        </div>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <button
                            @click="editing = !editing"
                            class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition"
                            x-text="editing ? 'Cancel' : 'Rename'"
                        ></button>
                        <button
                            wire:click="deleteCollection({{ $collection->id }})"
                            wire:confirm="Are you sure you want to delete '{{ $collection->name }}'? This action cannot be undone."
                            class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
