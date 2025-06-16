<?php

namespace App\Filament\Resources\MaintenanceRequestResource\Pages;

use App\Filament\Resources\MaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditMaintenanceRequest extends EditRecord
{
  protected static string $resource = MaintenanceRequestResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\ViewAction::make(),
    ];
  }

  protected function mutateFormDataBeforeSave(array $data): array
  {
    $originalStatus = $this->record->status;
    $newStatus = $data['status'];

    if ($originalStatus === 'pending' && $newStatus === 'approved') {
      $data['approved_at'] = now();
      $data['approved_by'] = auth()->id();
    }

    if ($originalStatus === 'approved' && $newStatus === 'completed') {
      $data['completed_at'] = now();
    }

    return $data;
  }

  protected function afterSave(): void
  {
    $originalStatus = $this->record->getOriginal('status');
    $newStatus = $this->record->status;

    if ($originalStatus === 'pending' && $newStatus === 'approved') {
      DB::transaction(function () {
        $this->record->vehicle->update(['status' => 'maintenance']);
      });
    }

    if ($originalStatus === 'approved' && $newStatus === 'completed') {
      DB::transaction(function () {
        $this->record->vehicle->update([
          'status' => 'active',
          'last_maintenance_date' => now(),
        ]);
      });
    }
  }
}
