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
use App\Filament\Tables\Columns\QRCodeColumn;
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
                    ->numeric()
                    ->live(),
                Forms\Components\TextInput::make('fuel_type'),
                Forms\Components\TextInput::make('transmission_type'),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\DatePicker::make('last_service_date')
                            ->label('Last PMS')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                if ($state) {
                                    // Set next service date to 4 months later
                                    $nextServiceDate = \Carbon\Carbon::parse($state)->addMonths(4)->format('Y-m-d');
                                    $set('next_service_due_date', $nextServiceDate);
                                    
                                    // Set odometer_during_last_service to current_mileage if not already set
                                    $currentMileage = $get('current_mileage');
                                    if ($currentMileage && !$get('odometer_during_last_service')) {
                                        $set('odometer_during_last_service', $currentMileage);
                                    }
                                    
                                    // Update estimated next service odometer if last odometer is set
                                    $lastOdometer = $get('odometer_during_last_service');
                                    if ($lastOdometer && !$get('estimated_next_service_odometer')) {
                                        $set('estimated_next_service_odometer', intval($lastOdometer) + 10000);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('odometer_during_last_service')
                            ->label('Odometer During Last Service')
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                if ($state) {
                                    // Update estimated next service odometer when last odometer changes
                                    $set('estimated_next_service_odometer', intval($state) + 10000);
                                }
                            })
                            ->disabled(fn (string $operation): bool => $operation === 'view')
                            ->dehydrated(fn ($state) => filled($state))
                            ->helperText('Auto-filled when last service date is selected'),
                    ]),
                Forms\Components\DatePicker::make('next_service_due_date')
                    ->label('Next PMS'),
                Forms\Components\TextInput::make('estimated_next_service_odometer')
                    ->label('Estimated Odometer for Next Service')
                    ->numeric()
                    ->disabled(fn (string $operation): bool => $operation === 'view')
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Auto-calculated as Last Odometer + 10,000 km'),
                Forms\Components\TextInput::make('insurance_provider'),
                Forms\Components\TextInput::make('insurance_policy_number'),
                Forms\Components\DatePicker::make('insurance_expiry_date'),
                
                // Registration Renewal Section
                Forms\Components\Section::make('Registration Renewal')
                    ->schema([
                        Forms\Components\DatePicker::make('last_registration_renewal')
                            ->label('Last Registration Renewal')
                            ->native(false)
                            ->displayFormat('M d, Y'),
                        Forms\Components\DatePicker::make('next_registration_renewal')
                            ->label('Next Registration Renewal')
                            ->native(false)
                            ->displayFormat('M d, Y'),
                        Forms\Components\TextInput::make('renewal_fee')
                            ->label('Renewal Fee')
                            ->numeric()
                            ->prefix('₱'),
                    ])
                    ->columns(3),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Remarks')
                    ->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                QRCodeColumn::make('qr_code')
                    ->label('QR Code')
                    ->size(300) // Increased size for better scanning
                    ->margin(10) // Add margin around the QR code
                    ->color('#000000') // Pure black for better contrast
                    ->bgColor('#FFFFFF') // Pure white background
                    ->errorCorrectionLevel('H') // Higher error correction
                    ->text(fn ($record) => route('vehicles.public.show', ['vehicle' => $record, 'qr' => true]))
                    ->viewMode('button')
                    ->searchable(false)
                    ->sortable(false)
                    ->extraAttributes(['class' => 'cursor-default'])
                    ->disableClick(),
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
                    ->label('Last PMS')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_service_due_date')
                    ->label('Next PMS')
                    ->date()
                    ->sortable(),
Tables\Columns\TextColumn::make('insurance_provider'),
                Tables\Columns\TextColumn::make('insurance_policy_number'),
                Tables\Columns\TextColumn::make('insurance_expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_registration_renewal')
                    ->label('Last Reg. Renewal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('next_registration_renewal')
                    ->label('Next Reg. Renewal')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => 
                        $record->next_registration_renewal && 
                        $record->next_registration_renewal->isPast() ? 'danger' : 'success'
                    ),
                Tables\Columns\TextColumn::make('renewal_fee')
                    ->label('Renewal Fee')
                    ->formatStateUsing(fn ($state) => $state ? '₱' . number_format($state, 2) : ''),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Remarks')
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
