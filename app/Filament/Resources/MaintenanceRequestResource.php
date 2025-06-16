<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceRequestResource\Pages;
use App\Models\MaintenanceRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class MaintenanceRequestResource extends Resource
{
  protected static ?string $model = MaintenanceRequest::class;

  protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

  protected static ?string $navigationGroup = 'Fleet Management';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('vehicle_id')
          ->relationship('vehicle', 'license_plate')
          ->required()
          ->disabled()
          ->dehydrated(),
        Forms\Components\Select::make('maintenance_type')
          ->options(MaintenanceRequest::getMaintenanceTypes())
          ->required()
          ->disabled()
          ->dehydrated(),
        Forms\Components\Textarea::make('description')
          ->required()
          ->disabled()
          ->dehydrated(),
        Forms\Components\Select::make('status')
          ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
          ])
          ->required()
          ->disabled(
            fn(MaintenanceRequest $record) =>
            $record && $record->status !== 'pending'
          ),
        Forms\Components\Textarea::make('notes')
          ->required(
            fn(MaintenanceRequest $record) =>
            $record && in_array($record->status, ['rejected', 'completed'])
          )
          ->maxLength(1000),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('vehicle.license_plate')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('maintenance_type')
          ->formatStateUsing(fn(string $state): string => MaintenanceRequest::getMaintenanceTypes()[$state] ?? $state)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('requester.name')
          ->searchable()
          ->sortable(),
        Tables\Columns\BadgeColumn::make('status')
          ->formatStateUsing(fn(string $state): string => ucfirst($state))
          ->colors([
            'warning' => 'pending',
            'success' => 'approved',
            'danger' => 'rejected',
            'info' => 'completed',
          ]),
        Tables\Columns\TextColumn::make('requested_at')
          ->dateTime()
          ->sortable(),
        Tables\Columns\TextColumn::make('approved_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('completed_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\SelectFilter::make('status')
          ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
          ]),
        Tables\Filters\SelectFilter::make('maintenance_type')
          ->options(MaintenanceRequest::getMaintenanceTypes()),
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Action::make('approve')
          ->icon('heroicon-o-check')
          ->color('success')
          ->requiresConfirmation()
          ->action(function (MaintenanceRequest $record) {
            $record->update([
              'status' => 'approved',
              'approved_at' => now(),
              'approved_by' => Auth::id(),
            ]);

            $record->vehicle->update(['status' => 'maintenance']);

            Notification::make()
              ->title('Maintenance request approved')
              ->success()
              ->send();
          })
          ->visible(
            fn(MaintenanceRequest $record) =>
            $record->status === 'pending' && Auth::user()->isAdmin()
          ),
        Action::make('reject')
          ->icon('heroicon-o-x-mark')
          ->color('danger')
          ->form([
            Forms\Components\Textarea::make('notes')
              ->label('Rejection Reason')
              ->required()
              ->minLength(10)
              ->maxLength(1000),
          ])
          ->action(function (MaintenanceRequest $record, array $data) {
            $record->update([
              'status' => 'rejected',
              'approved_at' => now(),
              'approved_by' => Auth::id(),
              'notes' => $data['notes'],
            ]);

            Notification::make()
              ->title('Maintenance request rejected')
              ->success()
              ->send();
          })
          ->visible(
            fn(MaintenanceRequest $record) =>
            $record->status === 'pending' && Auth::user()->isAdmin()
          ),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make()
            ->visible(fn() => false),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListMaintenanceRequests::route('/'),
      'view' => Pages\ViewMaintenanceRequest::route('/{record}'),
      'edit' => Pages\EditMaintenanceRequest::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
  {
    return parent::getEloquentQuery()
      ->when(
        !Auth::user()->isAdmin(),
        fn($query) => $query->where('user_id', Auth::id())
      );
  }
}
