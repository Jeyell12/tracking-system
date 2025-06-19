<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleModelResource\Pages;
use App\Models\VehicleModel;
use App\Models\VehicleBrand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleModelResource extends Resource
{
  protected static ?string $model = VehicleModel::class;

  protected static ?string $navigationIcon = 'heroicon-o-truck';

  protected static ?string $navigationGroup = 'Fleet Management';

  protected static ?int $navigationSort = 2;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('vehicle_brand_id')
          ->relationship('brand', 'name')
          ->required()
          ->searchable()
          ->preload()
          ->createOptionForm([
            Forms\Components\TextInput::make('name')
              ->required()
              ->unique('vehicle_brands', 'name'),
            Forms\Components\TextInput::make('description')
              ->maxLength(255),
            Forms\Components\Toggle::make('status')
              ->required()
              ->default(true),
          ]),
        Forms\Components\TextInput::make('name')
          ->required()
          ->maxLength(255)
          ->unique(
            table: 'vehicle_models',
            column: 'name',
            ignoreRecord: true,
            modifyRuleUsing: function (Forms\Get $get, ?VehicleModel $record) {
              return function ($query) use ($get, $record) {
                $brandName = $get('vehicle_brand_id');
                if ($brandName) {
                  $brand = VehicleBrand::where('name', $brandName)->first();
                  if ($brand) {
                    $query->where('vehicle_brand_id', $brand->id);
                  }
                }
              };
            }
          ),
        Forms\Components\Select::make('vehicle_type')
          ->label('Vehicle Type')
          ->options(VehicleModel::getVehicleTypes())
          ->required()
          ->searchable(),
        Forms\Components\Select::make('transmission_type')
          ->label('Transmission Type')
          ->options(VehicleModel::getTransmissionTypes())
          ->required()
          ->searchable(),
        Forms\Components\TagsInput::make('years')
          ->required()
          ->placeholder('Add year')
          ->suggestions(range(1900, date('Y') + 1))
          ->validationAttribute('years'),
        Forms\Components\Toggle::make('status')
          ->required()
          ->default(true),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('brand.name')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('name')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('vehicle_type')
          ->label('Type')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('transmission_type')
          ->label('Transmission')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('years')
          ->badge()
          ->searchable()
          ->sortable(),
        Tables\Columns\IconColumn::make('status')
          ->boolean()
          ->sortable(),
        Tables\Columns\TextColumn::make('vehicles_count')
          ->counts('vehicles')
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
        Tables\Filters\SelectFilter::make('brand')
          ->relationship('brand', 'name'),
        Tables\Filters\SelectFilter::make('vehicle_type')
          ->options(VehicleModel::getVehicleTypes()),
        Tables\Filters\SelectFilter::make('transmission_type')
          ->options(VehicleModel::getTransmissionTypes()),
        Tables\Filters\TernaryFilter::make('status'),
        Tables\Filters\TrashedFilter::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
        Tables\Actions\ForceDeleteAction::make(),
        Tables\Actions\RestoreAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
          Tables\Actions\ForceDeleteBulkAction::make(),
          Tables\Actions\RestoreBulkAction::make(),
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
      'index' => Pages\ListVehicleModels::route('/'),
      'create' => Pages\CreateVehicleModel::route('/create'),
      'edit' => Pages\EditVehicleModel::route('/{record}/edit'),
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
