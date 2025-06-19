<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPendingMaintenanceRequests extends BaseWidget
{
  protected static ?string $heading = 'Recent Pending Maintenance Requests';

  protected int | string | array $columnSpan = 'full';

  public function table(Table $table): Table
  {
    return $table
      ->query(
        MaintenanceRequest::query()
          ->where('status', 'pending')
          ->latest('requested_at')
          ->limit(10)
      )
      ->columns([
        Tables\Columns\TextColumn::make('vehicle.license_plate')
          ->label('Vehicle')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('maintenance_type')
          ->formatStateUsing(fn(string $state): string => MaintenanceRequest::getMaintenanceTypes()[$state] ?? $state)
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('requester.name')
          ->label('Requested By')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('requested_at')
          ->label('Requested')
          ->dateTime()
          ->sortable(),
        Tables\Columns\TextColumn::make('description')
          ->limit(50)
          ->searchable(),
      ])
      ->defaultSort('requested_at', 'desc')
      ->paginated(false);
  }
}
