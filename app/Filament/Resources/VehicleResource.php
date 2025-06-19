<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\Action;
use App\Filament\Resources\VehicleResource\Pages\ViewVehicle;
use Filament\Notifications\Notification;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Fleet Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('make')
                    ->relationship('brand', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('model', null))
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
                Forms\Components\Select::make('model')
                    ->relationship('model', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('year', null))
                    ->options(function (Forms\Get $get) {
                        $brand = $get('make');
                        if (!$brand) {
                            return [];
                        }
                        return VehicleModel::where('vehicle_brand_id', function ($query) use ($brand) {
                            $query->select('id')
                                ->from('vehicle_brands')
                                ->where('name', $brand)
                                ->limit(1);
                        })
                            ->where('status', true)
                            ->pluck('name', 'name');
                    })
                    ->createOptionForm([
                        Forms\Components\Select::make('vehicle_brand_id')
                            ->relationship('brand', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TagsInput::make('years')
                            ->required()
                            ->placeholder('Add year')
                            ->suggestions(range(1900, date('Y') + 1)),
                        Forms\Components\Toggle::make('status')
                            ->required()
                            ->default(true),
                    ]),
                Forms\Components\Select::make('year')
                    ->required()
                    ->live()
                    ->options(function (Forms\Get $get) {
                        $model = $get('model');
                        if (!$model) {
                            return [];
                        }
                        $vehicleModel = VehicleModel::where('name', $model)
                            ->where('status', true)
                            ->first();
                        if (!$vehicleModel) {
                            return [];
                        }
                        return collect($vehicleModel->years)
                            ->sort()
                            ->reverse()
                            ->mapWithKeys(fn($year) => [$year => $year])
                            ->toArray();
                    }),
                Forms\Components\TextInput::make('vin')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('license_plate')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('color'),
                Forms\Components\TextInput::make('vehicle_type')
                    ->label('Type'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                        'out_of_service' => 'Out of Service',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('current_mileage')
                    ->numeric(),
                Forms\Components\TextInput::make('fuel_type'),
                Forms\Components\TextInput::make('transmission_type'),
                Forms\Components\DatePicker::make('last_service_date'),
                Forms\Components\DatePicker::make('next_service_due_date'),
                Forms\Components\DatePicker::make('purchase_date'),
                Forms\Components\TextInput::make('purchase_price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('current_value')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('insurance_provider'),
                Forms\Components\TextInput::make('insurance_policy_number'),
                Forms\Components\DatePicker::make('insurance_expiry_date'),
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('license_plate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('make')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\TextColumn::make('vehicle_type')
                    ->label('Type'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'maintenance' => 'warning',
                        'out_of_service' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('current_mileage'),
                Tables\Columns\TextColumn::make('fuel_type'),
                Tables\Columns\TextColumn::make('transmission_type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_service_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_service_due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->money('usd'),
                Tables\Columns\TextColumn::make('current_value')
                    ->money('usd'),
                Tables\Columns\TextColumn::make('insurance_provider'),
                Tables\Columns\TextColumn::make('insurance_policy_number'),
                Tables\Columns\TextColumn::make('insurance_expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(20),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                        'out_of_service' => 'Out of Service',
                    ])
                    ->label('Vehicle Status')
                    ->indicator('Status')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'view' => Pages\ViewVehicle::route('/{record}'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }

    public static function getHeaderActions(): array
    {
        return [
            Action::make('requestMaintenance')
                ->label('Request Maintenance')
                ->hidden(fn(Vehicle $record) => !$record->canRequestMaintenance())
                ->action(function (Vehicle $record) {
                    // In a real scenario, you'd create a maintenance record (or trigger a notification) here.
                    // For now, we'll just notify the user.
                    Notification::make()
                        ->title('Maintenance Request Submitted')
                        ->body('A maintenance request for vehicle "' . $record->license_plate . '" has been submitted.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
