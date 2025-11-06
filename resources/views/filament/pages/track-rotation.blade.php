<x-filament-panels::page
    x-data
    @track-list-updated.window="$wire.$refresh()"
>
    <!-- AI Chat Widget -->
    @livewire('track-rotation-chat-widget')

    <div class="mb-4"
        x-data="{
            originalTracks: null,
            async init() {
                // Initialize with current form state from Livewire
                this.originalTracks = await @this.get('data.tracks');

                // Sync dropdown with Livewire property
                const collectionId = await @this.get('currentCollectionId');
                if ($refs.collectionSelect) {
                    $refs.collectionSelect.value = collectionId || '';
                }
            },
            async confirmSwitch(event) {
                const newValue = event.target.value;
                const currentTracks = await @this.get('data.tracks');

                // Normalize both for comparison (handle empty states)
                const currentStr = JSON.stringify(currentTracks || []);
                const originalStr = JSON.stringify(this.originalTracks || []);

                // Check if tracks have changed
                if (currentStr !== originalStr) {
                    if (!confirm('You have unsaved changes. Do you want to switch collections without saving?')) {
                        event.target.value = @js($this->currentCollectionId) || '';
                        return;
                    }
                }

                // Proceed with the switch
                await @this.set('currentCollectionId', newValue);

                // Wait a moment for Livewire to process and update
                await new Promise(resolve => setTimeout(resolve, 100));

                // Update baseline after switch completes
                this.originalTracks = await @this.get('data.tracks');
            }
        }"
        @collection-loaded.window="async (event) => {
            // Update the dropdown to reflect the newly loaded collection
            await new Promise(resolve => setTimeout(resolve, 100));
            const collectionId = await @this.get('currentCollectionId');
            $refs.collectionSelect.value = collectionId || '';

            // Update the baseline for comparison
            originalTracks = await @this.get('data.tracks');
        }"
        @collection-saved.window="originalTracks = $event.detail.tracks"
        @refresh-track-rotation.window="async () => {
            // Reload the currently selected collection
            if (@js($this->currentCollectionId)) {
                await @this.call('loadCollectionById', @js($this->currentCollectionId));
            }
        }"
    >
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Working on:
        </label>
        <select
            x-ref="collectionSelect"
            @change="confirmSwitch($event)"
            class="mt-1 block w-full max-w-md rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"
        >
            <option value="">Loaded from server (unsaved)</option>
            @foreach(\App\Models\TrackCollection::orderBy('updated_at', 'desc')->get() as $collection)
                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
            @endforeach
        </select>
    </div>

    {{ $this->form }}

    <x-filament-actions::modals />
</x-filament-panels::page>
