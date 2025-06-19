<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class AccountSettings extends Page
{
  use InteractsWithForms;

  protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
  protected static ?string $navigationLabel = 'Account Settings';
  protected static ?string $navigationGroup = 'Settings';
  protected static ?string $title = 'Account Settings';
  protected static ?int $navigationSort = 100;

  public $name;
  public $email;
  public $password;
  public $password_confirmation;

  public function mount(): void
  {
    $user = Auth::user();
    $this->name = $user->name;
    $this->email = $user->email;
    $this->form->fill([
      'name' => $this->name,
      'email' => $this->email,
    ]);
  }

  protected function getFormSchema(): array
  {
    return [
      Forms\Components\TextInput::make('name')
        ->label('Name')
        ->required(),
      Forms\Components\TextInput::make('email')
        ->label('Email')
        ->email()
        ->required(),
      Forms\Components\TextInput::make('password')
        ->label('New Password')
        ->password()
        ->minLength(8)
        ->maxLength(255)
        ->dehydrateStateUsing(fn($state) => !empty($state) ? Hash::make($state) : null)
        ->same('password_confirmation')
        ->nullable(),
      Forms\Components\TextInput::make('password_confirmation')
        ->label('Confirm New Password')
        ->password()
        ->maxLength(255)
        ->nullable(),
    ];
  }

  public function save()
  {
    $user = Auth::user();
    $data = $this->form->getState();
    $user->name = $data['name'];
    $user->email = $data['email'];
    if (!empty($data['password'])) {
      $user->password = $data['password'];
    }
    $user->save();
    Notification::make()
      ->title('Profile updated successfully!')
      ->success()
      ->send();
  }

  protected function getFormModel(): \Illuminate\Database\Eloquent\Model|string|null
  {
    return Auth::user();
  }

  protected function getFormStatePath(): string
  {
    return '';
  }
}
