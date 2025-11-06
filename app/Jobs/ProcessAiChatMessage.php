<?php

namespace App\Jobs;

use App\Agents\TrackCollectionAgent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class ProcessAiChatMessage implements ShouldQueue
{
    use Queueable;

    /**
     * The maximum number of seconds the job can run before timing out.
     * Set to 5 minutes to allow for slow AI responses.
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $messageId,
        public string $userMessage,
        public string $chatKey,
        public int $userId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Mark as processing
            Cache::put("ai_message_{$this->messageId}_status", 'processing', 360);

            // Get AI response
            $agent = new TrackCollectionAgent($this->chatKey);
            $response = $agent->respond($this->userMessage);

            // Store the response in cache (6 minutes expiry)
            Cache::put("ai_message_{$this->messageId}_response", $response, 360);
            Cache::put("ai_message_{$this->messageId}_status", 'completed', 360);
        } catch (\Exception $e) {
            // Store error in cache
            Cache::put("ai_message_{$this->messageId}_error", $e->getMessage(), 360);
            Cache::put("ai_message_{$this->messageId}_status", 'error', 360);

            // Re-throw to mark job as failed
            throw $e;
        }
    }
}
