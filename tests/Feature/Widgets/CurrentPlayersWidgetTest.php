<?php

use App\Filament\Widgets\CurrentPlayersWidget;
use App\Services\WreckfestApiClient;
use Livewire\Livewire;

beforeEach(function () {
    // Create a user and authenticate
    $this->actingAs(\App\Models\User::factory()->create());
});

it('displays message when no players are online', function () {
    // Mock the API client to return empty array
    $this->mock(WreckfestApiClient::class, function ($mock) {
        $mock->shouldReceive('getPlayers')
            ->once()
            ->andReturn([]);
    });

    // Render the widget
    Livewire::test(CurrentPlayersWidget::class)
        ->assertSee('No players are currently online')
        ->assertSee('Check back later or start the server to see players connect')
        ->assertDontSee('Total Players')
        ->assertDontSee('(BOT)');
});

it('displays players when they are online', function () {
    // Mock the API client to return players
    $this->mock(WreckfestApiClient::class, function ($mock) {
        $mock->shouldReceive('getPlayers')
            ->once()
            ->andReturn([
                ['name' => 'Player1', 'isBot' => false],
                ['name' => 'Player2', 'isBot' => false],
            ]);
    });

    // Render the widget
    Livewire::test(CurrentPlayersWidget::class)
        ->assertSee('Player1')
        ->assertSee('Player2')
        ->assertDontSee('No players currently online');
});

it('identifies and displays bots correctly', function () {
    // Mock the API client to return a mix of players and bots
    $this->mock(WreckfestApiClient::class, function ($mock) {
        $mock->shouldReceive('getPlayers')
            ->once()
            ->andReturn([
                ['name' => 'RealPlayer', 'isBot' => false],
                ['name' => 'BotPlayer', 'isBot' => true],
            ]);
    });

    // Render the widget
    Livewire::test(CurrentPlayersWidget::class)
        ->assertSee('RealPlayer')
        ->assertSee('(BOT) BotPlayer');
});

it('handles IsBot key with capital B', function () {
    // Mock the API client to return bot with capital B in key
    $this->mock(WreckfestApiClient::class, function ($mock) {
        $mock->shouldReceive('getPlayers')
            ->once()
            ->andReturn([
                ['name' => 'BotPlayer', 'IsBot' => true],
            ]);
    });

    // Render the widget
    $component = Livewire::test(CurrentPlayersWidget::class);

    expect($component->instance()->isBot(['name' => 'BotPlayer', 'IsBot' => true]))->toBeTrue();
});

it('formats player names correctly', function () {
    $this->mock(WreckfestApiClient::class, function ($mock) {
        $mock->shouldReceive('getPlayers')
            ->once()
            ->andReturn([]);
    });

    $widget = Livewire::test(CurrentPlayersWidget::class)->instance();

    // Test regular player
    expect($widget->formatPlayerName(['name' => 'TestPlayer', 'isBot' => false]))
        ->toBe('TestPlayer');

    // Test bot player
    expect($widget->formatPlayerName(['name' => 'BotPlayer', 'isBot' => true]))
        ->toBe('(BOT) BotPlayer');

    // Test string player name
    expect($widget->formatPlayerName('StringPlayer'))
        ->toBe('StringPlayer');

    // Test missing name
    expect($widget->formatPlayerName(['isBot' => false]))
        ->toBe('Unknown Player');
});

it('refreshes players when refresh method is called', function () {
    $mock = $this->mock(WreckfestApiClient::class);

    // First call on mount
    $mock->shouldReceive('getPlayers')
        ->once()
        ->andReturn(['player1']);

    // Second call on refresh
    $mock->shouldReceive('getPlayers')
        ->once()
        ->andReturn(['player1', 'player2']);

    Livewire::test(CurrentPlayersWidget::class)
        ->call('refreshPlayers')
        ->assertSet('players', ['player1', 'player2']);
});

it('is visible to authenticated users', function () {
    expect(CurrentPlayersWidget::canView())->toBeTrue();
});
