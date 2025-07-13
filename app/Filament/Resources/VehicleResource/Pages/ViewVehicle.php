<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use App\Models\MaintenanceRequest;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;

class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Vehicle Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('vin')
                            ->label('VIN'),
                        Infolists\Components\TextEntry::make('license_plate')
                            ->label('License Plate'),
                        Infolists\Components\TextEntry::make('make'),
                        Infolists\Components\TextEntry::make('model'),
                        Infolists\Components\TextEntry::make('year'),
                        Infolists\Components\TextEntry::make('color'),
                        Infolists\Components\TextEntry::make('vehicle_type')
                            ->label('Type'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'active' => 'success',
                                'maintenance' => 'warning',
                                'out_of_service' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('Technical Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('current_mileage')
                            ->label('Current Mileage'),
                        Infolists\Components\TextEntry::make('fuel_type')
                            ->label('Fuel Type'),
                        Infolists\Components\TextEntry::make('transmission_type')
                            ->label('Transmission Type'),
                        Infolists\Components\TextEntry::make('last_service_date')
                            ->label('Last PMS')
                            ->date(),
                        Infolists\Components\TextEntry::make('next_service_due_date')
                            ->label('Next PMS')
                            ->date(),
                    ])->columns(2),

                Infolists\Components\Section::make('Insurance Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('insurance_provider')
                            ->label('Insurance Provider'),
                        Infolists\Components\TextEntry::make('insurance_policy_number')
                            ->label('Policy Number'),
                        Infolists\Components\TextEntry::make('insurance_expiry_date')
                            ->label('Expiry Date')
                            ->date(),
                    ])->columns(2),

                Infolists\Components\Section::make('Registration Renewal')
                    ->schema([
                        Infolists\Components\TextEntry::make('last_registration_renewal')
                            ->label('Last Registration Renewal')
                            ->date(),
                        Infolists\Components\TextEntry::make('next_registration_renewal')
                            ->label('Next Registration Renewal')
                            ->date()
                            ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'success'),
                        Infolists\Components\TextEntry::make('renewal_fee')
                            ->label('Renewal Fee')
                            ->formatStateUsing(fn ($state) => $state ? '₱' . number_format($state, 2) : ''),
                    ])->columns(3),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Remarks')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->url(fn() => VehicleResource::getUrl('edit', ['record' => $this->record]))
                ->icon('heroicon-o-pencil-square'),
            Action::make('requestMaintenance')
                ->label('Request Maintenance')
                ->icon('heroicon-o-wrench-screwdriver')
                ->form([
                    Forms\Components\Select::make('maintenance_type')
                        ->label('Maintenance Type')
                        ->options(MaintenanceRequest::getMaintenanceTypes())
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->minLength(10)
                        ->maxLength(1000),
                ])
                ->action(function (array $data): void {
                    if ($this->record->status === 'maintenance') {
                        Notification::make()
                            ->title('Vehicle is already in maintenance')
                            ->danger()
                            ->send();
                        return;
                    }

                    MaintenanceRequest::create([
                        'vehicle_id' => $this->record->id,
                        'user_id' => Auth::id(),
                        'maintenance_type' => $data['maintenance_type'],
                        'description' => $data['description'],
                        'status' => 'pending',
                        'requested_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Maintenance request submitted successfully')
                        ->success()
                        ->send();
                })
                ->visible(fn() => $this->record->status !== 'maintenance'),
            Action::make('viewMaintenanceHistory')
                ->label('View Maintenance History')
                ->icon('heroicon-o-clock')
                ->modalHeading('Maintenance History')
                ->modalSubmitActionLabel('Close')
                ->modalCancelAction(false)
                ->modalContent(function () {
                    if (!$this->record->maintenanceRequests()->exists()) {
                        return view('filament.components.no-maintenance-history');
                    }

                    $maintenanceRequests = $this->record->maintenanceRequests()
                        ->with(['requester', 'vehicle'])
                        ->latest('requested_at')
                        ->limit(5)
                        ->get();

                    return view('filament.components.maintenance-history-modal', [
                        'maintenanceRequests' => $maintenanceRequests,
                        'vehicleId' => $this->record->id
                    ]);
                })
                ->action(function () {
                    // This is intentionally left empty as we're using the modal content
                }),
        ];
    }
}
