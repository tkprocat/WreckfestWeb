<?php

namespace App\Livewire;

use App\Agents\TrackCollectionAgent;
use App\Jobs\ProcessAiChatMessage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;

class TrackRotationChatWidget extends Component
{
    public string $message = '';

    public array $messages = [];

    public bool $isMinimized = false;

    public ?string $pendingMessageId = null;

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
        $sessionKey = 'track_rotation_chat_messages_'.auth()->id();
        session([$sessionKey => $this->messages]);

        // Check if we should use async processing (defaults to true)
        $useAsync = config('wreckfest.ai_async', true);

        if ($useAsync) {
            // Async mode - dispatch job and poll for response
            $this->sendMessageAsync($userMessage);
        } else {
            // Sync mode - block until response
            $this->sendMessageSync($userMessage);
        }
    }

    protected function sendMessageSync(string $userMessage): void
    {
        try {
            // Get AI response - this will block until complete
            // Use authenticated user ID for proper memory isolation per user
            $chatKey = 'user_'.auth()->id();
            $agent = new TrackCollectionAgent($chatKey);
            $response = $agent->respond($userMessage);

            // Add assistant response
            $this->addAssistantResponse($response);
        } catch (\Exception $e) {
            $this->handleAiError($e);
        }
    }

    protected function sendMessageAsync(string $userMessage): void
    {
        try {
            // Generate unique message ID
            $messageId = Str::uuid()->toString();
            $this->pendingMessageId = $messageId;

            // Add pending message placeholder
            $this->messages[] = [
                'role' => 'assistant',
                'content' => '⏳ Thinking...',
                'timestamp' => now()->toDateTimeString(),
                'pending' => true,
                'messageId' => $messageId,
            ];

            // Save to session
            $sessionKey = 'track_rotation_chat_messages_'.auth()->id();
            session([$sessionKey => $this->messages]);

            // Dispatch job to process in background
            $chatKey = 'user_'.auth()->id();
            ProcessAiChatMessage::dispatch($messageId, $userMessage, $chatKey, auth()->id());

            // The frontend will poll checkPendingResponse() to get the result
        } catch (\Exception $e) {
            $this->handleAiError($e);
        }
    }

    public function checkPendingResponse(): void
    {
        if (! $this->pendingMessageId) {
            return;
        }

        $messageId = $this->pendingMessageId;
        $status = Cache::get("ai_message_{$messageId}_status");

        if ($status === 'completed') {
            // Get the response
            $response = Cache::get("ai_message_{$messageId}_response");

            // Remove pending message
            $this->messages = array_filter($this->messages, function ($msg) use ($messageId) {
                return ! isset($msg['messageId']) || $msg['messageId'] !== $messageId;
            });
            $this->messages = array_values($this->messages);

            // Add real response
            $this->addAssistantResponse($response);

            // Clear cache
            Cache::forget("ai_message_{$messageId}_status");
            Cache::forget("ai_message_{$messageId}_response");

            // Clear pending ID
            $this->pendingMessageId = null;
        } elseif ($status === 'error') {
            // Get error message
            $error = Cache::get("ai_message_{$messageId}_error", 'Unknown error occurred');

            // Remove pending message
            $this->messages = array_filter($this->messages, function ($msg) use ($messageId) {
                return ! isset($msg['messageId']) || $msg['messageId'] !== $messageId;
            });
            $this->messages = array_values($this->messages);

            // Show error
            Notification::make()
                ->title('Error')
                ->body('Failed to get AI response: '.$error)
                ->danger()
                ->send();

            // Clear cache
            Cache::forget("ai_message_{$messageId}_status");
            Cache::forget("ai_message_{$messageId}_error");

            // Clear pending ID
            $this->pendingMessageId = null;

            // Save cleaned messages to session
            $sessionKey = 'track_rotation_chat_messages_'.auth()->id();
            session([$sessionKey => $this->messages]);
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
