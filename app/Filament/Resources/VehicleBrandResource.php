<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleBrandResource\Pages;
use App\Models\VehicleBrand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleBrandResource extends Resource
{
  protected static ?string $model = VehicleBrand::class;

  protected static ?string $navigationIcon = 'heroicon-o-tag';

  protected static ?string $navigationGroup = 'Fleet Management';

  protected static ?int $navigationSort = 3;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\TextInput::make('name')
          ->required()
          ->unique(ignoreRecord: true)
          ->maxLength(255),
        Forms\Components\Textarea::make('description')
          ->maxLength(65535)
          ->columnSpanFull(),
        Forms\Components\Toggle::make('status')
          ->required()
          ->default(true)
          ->label('Active'),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('description')
          ->limit(50)
          ->searchable(),
        Tables\Columns\IconColumn::make('status')
          ->boolean()
          ->label('Active')
          ->sortable(),
        Tables\Columns\TextColumn::make('vehicles_count')
          ->counts('vehicles')
          ->label('Vehicles')
          ->sortable(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        Tables\Filters\TrashedFilter::make(),
        Tables\Filters\SelectFilter::make('status')
          ->options([
            '1' => 'Active',
            '0' => 'Inactive',
          ])
          ->label('Status'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
        Tables\Actions\RestoreAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
          Tables\Actions\RestoreBulkAction::make(),
          Tables\Actions\ForceDeleteBulkAction::make(),
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
      'index' => Pages\ListVehicleBrands::route('/'),
      'create' => Pages\CreateVehicleBrand::route('/create'),
      'edit' => Pages\EditVehicleBrand::route('/{record}/edit'),
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->withoutGlobalScopes([
        SoftDeletingScope::class,
      ]);
  }
}
