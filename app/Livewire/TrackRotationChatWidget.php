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
        $sessionKey = 'track_rotation_chat_messages_'.auth()->id();
        $this->messages = session($sessionKey, []);
    }

    public function sendMessage(?string $userMessage = null): void
    {
        // If no parameter provided, use the property
        $userMessage = $userMessage ?? $this->message;

        if (empty($userMessage)) {
            return;
        }

        // Clear the input field
        $this->message = '';

        // Add user message immediately
        $this->messages[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Force Livewire to detect the array change
        $this->messages = array_values($this->messages);

        // Save messages to session (per user)
        $sessionKey = 'track_rotation_chat_messages_'.auth()->id();
        session([$sessionKey => $this->messages]);

        // Dispatch event to ensure UI updates immediately with user message
        $this->dispatch('messages-updated', count: count($this->messages));

        // Stream AI response using Livewire's wire:stream
        try {
            $chatKey = 'user_'.auth()->id();
            $agent = new TrackCollectionAgent($chatKey);

            logger()->info('[Chat] About to call respondStreamed', ['message' => $userMessage]);

            // Use respondStreamed for streaming response
            $fullResponse = '';
            $chunkCount = 0;
            foreach ($agent->respondStreamed($userMessage) as $chunk) {
                $chunkCount++;

                // Check chunk type and log for debugging
                logger()->info('[Chat] Received chunk', [
                    'chunk_number' => $chunkCount,
                    'type' => get_class($chunk),
                    'chunk' => method_exists($chunk, 'getLastChunk') ? $chunk->getLastChunk() : 'no method',
                ]);

                // Handle StreamedAssistantMessage
                if (method_exists($chunk, 'getLastChunk')) {
                    $delta = $chunk->getLastChunk();

                    if ($delta && is_string($delta) && $delta !== '') {
                        $fullResponse .= $delta;

                        // Stream each chunk to the browser
                        $this->stream(
                            to: 'ai-response',
                            content: $delta,
                            replace: false
                        );
                    }
                }
            }

            logger()->info('[Chat] Finished streaming', [
                'total_chunks' => $chunkCount,
                'response_length' => strlen($fullResponse)
            ]);

            // Save the complete response
            if ($fullResponse) {
                $this->addAssistantResponse($fullResponse);
            } else {
                logger()->warning('[Chat] No response received from agent');
            }
        } catch (\Exception $e) {
            logger()->error('[TrackRotationChat] AI request failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->handleAiError($e);
        }
    }

    protected function addAssistantResponse(string $response): void
    {
        // Add assistant response
        $assistantMessage = [
            'role' => 'assistant',
            'content' => $response,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Force Livewire to detect the array change by creating a new array
        $this->messages = [
            ...$this->messages,
            $assistantMessage,
        ];

        // Save to session (per user)
        $sessionKey = 'track_rotation_chat_messages_'.auth()->id();
        session([$sessionKey => $this->messages]);

        // Force a fresh component state by dispatching a browser event
        $this->dispatch('messages-updated', count: count($this->messages));

        // Check if a new collection was created and auto-select it
        $newCollectionLoaded = $this->checkForNewCollection();

        // Only dispatch refresh if we didn't just load a new collection
        if (! $newCollectionLoaded) {
            $this->dispatch('refresh-track-rotation');
        }
    }

    protected function handleAiError(\Exception $e): void
    {
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

    public function clearChat(): void
    {
        $this->messages = [];
        $sessionKey = 'track_rotation_chat_messages_'.auth()->id();
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

        if ($collectionId) {

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
