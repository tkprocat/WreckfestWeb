<?php

namespace App\Filament\Pages\Auth;

use App\Models\Invitation;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Register extends \Filament\Auth\Pages\Register
{
    protected ?string $inviteToken = null;

    public function mount(): void
    {
        $this->inviteToken = request()->query('invite');

        if (! $this->inviteToken) {
            Notification::make()
                ->title('Invitation required')
                ->body('You need an invitation to register.')
                ->danger()
                ->send();

            $this->redirect('/admin/login');

            return;
        }

        // Validate invitation token
        $invitation = Invitation::where('token', $this->inviteToken)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitation) {
            Notification::make()
                ->title('Invalid invitation')
                ->body('This invitation is invalid or has expired.')
                ->danger()
                ->send();

            $this->redirect('/admin/login');

            return;
        }

        // Pre-fill email from invitation
        $this->form->fill([
            'email' => $invitation->email,
        ]);

        parent::mount();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent()->disabled(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(User::class)
            ->disabled()
            ->helperText('Email is pre-filled from your invitation');
    }

    protected function handleRegistration(array $data): User
    {
        // Validate invitation one more time
        $invitation = Invitation::where('token', $this->inviteToken)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitation) {
            throw ValidationException::withMessages([
                'email' => 'This invitation is no longer valid.',
            ]);
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
        ]);

        // Mark invitation as accepted
        $invitation->update([
            'accepted_at' => now(),
        ]);

        return $user;
    }

    public function getHeading(): string|Htmlable
    {
        return 'Create your account';
    }

    public function getSubHeading(): string|Htmlable|null
    {
        return 'You\'ve been invited to join '.config('app.name');
    }
}
