<?php

use App\Livewire\TrackRotationChatWidget;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Passkey authentication routes
Route::passkeys();

// AI Chat streaming endpoint
// This endpoint handles streaming responses from the LarAgent
Route::post('/api/chat/stream', [TrackRotationChatWidget::class, 'streamChat'])
    ->middleware(['web', 'auth'])
    ->name('chat.stream');
