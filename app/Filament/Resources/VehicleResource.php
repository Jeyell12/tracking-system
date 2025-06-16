<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('vin')->required()->unique(),
                Forms\Components\TextInput::make('license_plate')->required()->unique(),
                Forms\Components\TextInput::make('make')->required(),
                Forms\Components\TextInput::make('model')->required(),
                Forms\Components\TextInput::make('year')->numeric()->required(),
                Forms\Components\TextInput::make('color'),
                Forms\Components\TextInput::make('vehicle_type')->label('Type'),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'maintenance' => 'Maintenance',
                        'out_of_service' => 'Out of Service',
                    ])->required(),
                Forms\Components\TextInput::make('current_mileage')->numeric(),
                Forms\Components\TextInput::make('fuel_type'),
                Forms\Components\TextInput::make('transmission_type'),
                Forms\Components\DatePicker::make('last_service_date'),
                Forms\Components\DatePicker::make('next_service_due_date'),
                Forms\Components\DatePicker::make('purchase_date'),
                Forms\Components\TextInput::make('purchase_price')->numeric()->prefix('$'),
                Forms\Components\TextInput::make('current_value')->numeric()->prefix('$'),
                Forms\Components\TextInput::make('insurance_provider'),
                Forms\Components\TextInput::make('insurance_policy_number'),
                Forms\Components\DatePicker::make('insurance_expiry_date'),
                Forms\Components\Textarea::make('notes')->rows(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vin')->searchable(),
                Tables\Columns\TextColumn::make('license_plate')->searchable(),
                Tables\Columns\TextColumn::make('make')->searchable(),
                Tables\Columns\TextColumn::make('model')->searchable(),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\TextColumn::make('vehicle_type')->label('Type'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('current_mileage'),
                Tables\Columns\TextColumn::make('fuel_type'),
                Tables\Columns\TextColumn::make('transmission_type'),
                Tables\Columns\TextColumn::make('last_service_date')->date(),
                Tables\Columns\TextColumn::make('next_service_due_date')->date(),
                Tables\Columns\TextColumn::make('purchase_date')->date(),
                Tables\Columns\TextColumn::make('purchase_price')->money('usd'),
                Tables\Columns\TextColumn::make('current_value')->money('usd'),
                Tables\Columns\TextColumn::make('insurance_provider'),
                Tables\Columns\TextColumn::make('insurance_policy_number'),
                Tables\Columns\TextColumn::make('insurance_expiry_date')->date(),
                Tables\Columns\TextColumn::make('notes')->limit(20),
            ])
            ->filters([
                //
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
