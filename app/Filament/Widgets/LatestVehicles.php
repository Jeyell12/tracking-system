<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestVehicles extends BaseWidget
{
  protected static ?string $heading = 'Latest Vehicles';

  protected int | string | array $columnSpan = 'full';

  public function table(Table $table): Table
  {
    return $table
      ->query(
        Vehicle::query()
          ->latest('created_at')
          ->limit(10)
      )
      ->columns([
        Tables\Columns\TextColumn::make('license_plate')
          ->label('License Plate')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('make')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('model')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('year')
          ->sortable(),
        Tables\Columns\TextColumn::make('vehicle_type')
          ->label('Type')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('status')
          ->badge()
          ->color(fn(string $state): string => match ($state) {
            'active' => 'success',
            'maintenance' => 'warning',
            'out_of_service' => 'danger',
            default => 'gray',
          })
          ->sortable(),
        Tables\Columns\TextColumn::make('current_mileage')
          ->label('Mileage')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->label('Added')
          ->dateTime()
          ->sortable(),
      ])
      ->defaultSort('created_at', 'desc')
      ->paginated(false);
  }
}
