<?php

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->create();
});

describe('Invitation Creation', function () {
    test('admin can access user management page', function () {
        $this->actingAs($this->admin)
            ->get('/admin/user-management')
            ->assertSuccessful();
    });

    test('invitation can be created', function () {
        $invitation = Invitation::create([
            'email' => 'test@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->addDays(7),
        ]);

        expect($invitation)->toBeInstanceOf(Invitation::class)
            ->and($invitation->email)->toBe('test@example.com')
            ->and($invitation->token)->not->toBeEmpty()
            ->and($invitation->invited_by)->toBe($this->admin->id);
    });

    test('invitation has correct relationships', function () {
        $invitation = Invitation::create([
            'email' => 'test@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->addDays(7),
        ]);

        expect($invitation->inviter)->toBeInstanceOf(User::class)
            ->and($invitation->inviter->id)->toBe($this->admin->id);
    });
});

describe('Invitation Validation', function () {
    test('can detect expired invitation', function () {
        $invitation = Invitation::create([
            'email' => 'test@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->subDay(),
        ]);

        expect($invitation->isExpired())->toBeTrue();
    });

    test('can detect valid invitation', function () {
        $invitation = Invitation::create([
            'email' => 'test@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->addDays(7),
        ]);

        expect($invitation->isExpired())->toBeFalse();
    });

    test('can detect accepted invitation', function () {
        $invitation = Invitation::create([
            'email' => 'test@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->addDays(7),
            'accepted_at' => now(),
        ]);

        expect($invitation->isAccepted())->toBeTrue();
    });

    test('can detect pending invitation', function () {
        $invitation = Invitation::create([
            'email' => 'test@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->addDays(7),
        ]);

        expect($invitation->isAccepted())->toBeFalse();
    });
});

describe('Registration with Invitation', function () {
    test('registration page requires invitation token', function () {
        $this->get('/admin/register')
            ->assertRedirect('/admin/login');
    });

    test('registration page accepts valid invitation token', function () {
        $invitation = Invitation::create([
            'email' => 'newuser@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->addDays(7),
        ]);

        $this->get('/admin/register?invite=' . $invitation->token)
            ->assertSuccessful();
    });

    test('cannot register with expired invitation', function () {
        $invitation = Invitation::create([
            'email' => 'newuser@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->subDay(),
        ]);

        $this->get('/admin/register?invite=' . $invitation->token)
            ->assertRedirect('/admin/login');
    });

    test('cannot register with accepted invitation', function () {
        $invitation = Invitation::create([
            'email' => 'newuser@example.com',
            'token' => Invitation::generateToken(),
            'invited_by' => $this->admin->id,
            'expires_at' => now()->addDays(7),
            'accepted_at' => now(),
        ]);

        $this->get('/admin/register?invite=' . $invitation->token)
            ->assertRedirect('/admin/login');
    });
});

describe('Token Generation', function () {
    test('generates unique tokens', function () {
        $token1 = Invitation::generateToken();
        $token2 = Invitation::generateToken();

        expect($token1)->not->toBe($token2)
            ->and(strlen($token1))->toBe(32)
            ->and(strlen($token2))->toBe(32);
    });
});
