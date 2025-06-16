<?php

namespace App\Filament\Resources\MaintenanceRequestResource\Pages;

use App\Filament\Resources\MaintenanceRequestResource;
use App\Models\MaintenanceRequest;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;

class ViewMaintenanceRequest extends ViewRecord
{
  protected static string $resource = MaintenanceRequestResource::class;

  public function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Infolists\Components\Section::make('Vehicle Information')
          ->schema([
            Infolists\Components\TextEntry::make('vehicle.license_plate')
              ->label('License Plate')
              ->badge(),
            Infolists\Components\TextEntry::make('vehicle.make')
              ->label('Make'),
            Infolists\Components\TextEntry::make('vehicle.model')
              ->label('Model'),
            Infolists\Components\TextEntry::make('vehicle.year')
              ->label('Year'),
          ])->columns(4),

        Infolists\Components\Section::make('Request Information')
          ->schema([
            Infolists\Components\TextEntry::make('maintenance_type')
              ->label('Maintenance Type')
              ->formatStateUsing(fn(string $state): string => MaintenanceRequest::getMaintenanceTypes()[$state] ?? $state)
              ->badge()
              ->color('info'),
            Infolists\Components\TextEntry::make('status')
              ->badge()
              ->color(fn(string $state): string => match ($state) {
                'pending' => 'warning',
                'approved' => 'success',
                'rejected' => 'danger',
                'completed' => 'info',
                default => 'gray',
              })
              ->formatStateUsing(fn(string $state): string => ucfirst($state)),
            Infolists\Components\TextEntry::make('description')
              ->label('Description')
              ->columnSpanFull(),
          ])->columns(2),

        Infolists\Components\Section::make('Timeline')
          ->schema([
            Infolists\Components\TextEntry::make('requested_at')
              ->label('Requested At')
              ->dateTime()
              ->icon('heroicon-o-clock'),
            Infolists\Components\TextEntry::make('requester.name')
              ->label('Requested By')
              ->icon('heroicon-o-user'),
            Infolists\Components\TextEntry::make('approved_at')
              ->label('Approved At')
              ->dateTime()
              ->icon('heroicon-o-check-circle')
              ->visible(fn($record) => $record->status !== 'pending'),
            Infolists\Components\TextEntry::make('approver.name')
              ->label('Approved By')
              ->icon('heroicon-o-user-circle')
              ->visible(fn($record) => $record->status !== 'pending'),
            Infolists\Components\TextEntry::make('completed_at')
              ->label('Completed At')
              ->dateTime()
              ->icon('heroicon-o-check-badge')
              ->visible(fn($record) => $record->status === 'completed'),
          ])->columns(2),

        Infolists\Components\Section::make('Additional Information')
          ->schema([
            Infolists\Components\TextEntry::make('notes')
              ->label(fn($record) => $record->status === 'rejected' ? 'Rejection Reason' : 'Notes')
              ->columnSpanFull()
              ->visible(fn($record) => $record->notes !== null),
          ]),
      ]);
  }

  protected function getHeaderActions(): array
  {
    $actions = [];

    if ($this->record->status === 'pending' && Auth::user()->isAdmin()) {
      $actions[] = Actions\Action::make('approve')
        ->icon('heroicon-o-check')
        ->color('success')
        ->requiresConfirmation()
        ->action(function () {
          $this->record->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
          ]);

          $this->record->vehicle->update(['status' => 'maintenance']);

          $this->notify('success', 'Maintenance request approved');
        });

      $actions[] = Actions\Action::make('reject')
        ->icon('heroicon-o-x-mark')
        ->color('danger')
        ->form([
          \Filament\Forms\Components\Textarea::make('notes')
            ->label('Rejection Reason')
            ->required()
            ->minLength(10)
            ->maxLength(1000),
        ])
        ->action(function (array $data) {
          $this->record->update([
            'status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'notes' => $data['notes'],
          ]);

          $this->notify('success', 'Maintenance request rejected');
        });
    }

    return $actions;
  }
}
