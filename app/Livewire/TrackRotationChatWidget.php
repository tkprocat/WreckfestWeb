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
        // Store chat messages per authenticated user
        $sessionKey = 'track_rotation_chat_messages_' . auth()->id();
        $this->messages = session($sessionKey, []);
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

        // Save messages to session (per user)
        $sessionKey = 'track_rotation_chat_messages_' . auth()->id();
        session([$sessionKey => $this->messages]);

        try {
            // Get AI response - this will block until complete
            // Use authenticated user ID for proper memory isolation per user
            $chatKey = 'user_' . auth()->id();
            $agent = new TrackCollectionAgent($chatKey);
            $response = $agent->respond($userMessage);

            // Add assistant response
            $assistantMessage = [
                'role' => 'assistant',
                'content' => $response,
                'timestamp' => now()->toDateTimeString(),
            ];

            // Force Livewire to detect the array change by creating a new array
            $this->messages = [
                ...$this->messages,
                $assistantMessage
            ];

            // Save to session (per user)
            $sessionKey = 'track_rotation_chat_messages_' . auth()->id();
            session([$sessionKey => $this->messages]);

            // Force a fresh component state by dispatching a browser event
            $this->dispatch('messages-updated', count: count($this->messages));

            // Log for debugging in production
            logger()->info('AI response added', [
                'message_count' => count($this->messages),
                'last_message_role' => $this->messages[count($this->messages) - 1]['role'] ?? 'unknown',
                'response_length' => strlen($response),
            ]);

            // Check if a new collection was created and auto-select it
            $newCollectionLoaded = $this->checkForNewCollection();

            // Only dispatch refresh if we didn't just load a new collection
            if (!$newCollectionLoaded) {
                $this->dispatch('refresh-track-rotation');
            }
        } catch (\Exception $e) {
            logger()->error('AI response error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->title('Error')
                ->body('Failed to get AI response: '.$e->getMessage())
                ->danger()
                ->send();

            // Remove the user message if there was an error
            array_pop($this->messages);
            $this->messages = array_values($this->messages);
        }
    }

    public function clearChat(): void
    {
        $this->messages = [];
        $sessionKey = 'track_rotation_chat_messages_' . auth()->id();
        session()->forget($sessionKey);
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
