<?php

namespace App\Livewire;

use App\Agents\TrackCollectionAgent;
use Filament\Notifications\Notification;
use Livewire\Component;

class TrackRotationChatWidget extends Component
{
    public string $message = '';

    public array $messages = [];

    public bool $isMinimized = false;

    public function mount(): void
    {
        $this->messages = session('track_rotation_chat_messages', []);
    }

    public function sendMessage(): void
    {
        if (empty($this->message)) {
            return;
        }

        // Store user message
        $userMessage = $this->message;

        // Add user message immediately
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Clear input
        $this->message = '';

        // Save messages to session
        session(['track_rotation_chat_messages' => $this->messages]);

        try {
            // Get AI response - this will block until complete
            $chatKey = session()->getId();
            $agent = new TrackCollectionAgent($chatKey);
            $response = $agent->respond($userMessage);

            // Add assistant response
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response,
                'timestamp' => now()->toDateTimeString(),
            ];

            // Save to session
            session(['track_rotation_chat_messages' => $this->messages]);

            // Check if a new collection was created and auto-select it
            $newCollectionLoaded = $this->checkForNewCollection();

            // Only dispatch refresh if we didn't just load a new collection
            if (!$newCollectionLoaded) {
                $this->dispatch('refresh-track-rotation');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Failed to get AI response: '.$e->getMessage())
                ->danger()
                ->send();

            // Remove the user message if there was an error
            array_pop($this->messages);
        }
    }

    public function clearChat(): void
    {
        $this->messages = [];
        session()->forget('track_rotation_chat_messages');
    }

    public function toggleMinimize(): void
    {
        $this->isMinimized = ! $this->isMinimized;
    }

    /**
     * Check if the AI created a new collection via MCP tools
     * and auto-switch to it
     *
     * @return bool True if a new collection was loaded, false otherwise
     */
    protected function checkForNewCollection(): bool
    {
        // Check if the MCP tool stored a newly created collection ID in the cache
        // (MCP runs in a different session context, so we use cache instead)
        $collectionId = cache('last_created_collection_id');

        logger()->info('Checking for new collection', [
            'collection_id' => $collectionId,
            'cache_has_key' => cache()->has('last_created_collection_id')
        ]);

        if ($collectionId) {
            logger()->info('Auto-switching to collection', ['id' => $collectionId]);

            // Clear the cache flag
            cache()->forget('last_created_collection_id');

            // Dispatch event to switch to this collection
            $this->dispatch('load-collection', collectionId: $collectionId);

            Notification::make()
                ->title('Collection Loaded')
                ->body("Now working on the newly created collection (ID: {$collectionId})")
                ->success()
                ->send();

            return true;
        }

        return false;
    }

    public function render()
    {
        return view('livewire.track-rotation-chat-widget');
    }
}
