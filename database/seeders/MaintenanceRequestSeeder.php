<?php

namespace Database\Seeders;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MaintenanceRequestSeeder extends Seeder
{
  public function run(): void
  {
    $maintenanceTypes = array_keys(MaintenanceRequest::getMaintenanceTypes());
    $statuses = ['pending', 'approved', 'rejected', 'completed'];
    $descriptions = [
      'Regular maintenance check required',
      'Vehicle making unusual noise',
      'Warning light on dashboard',
      'Scheduled maintenance due',
      'Performance issues reported',
      'Routine service required',
      'Vehicle inspection needed',
      'Preventive maintenance check'
    ];

    // Get all users and vehicles
    $users = User::all();
    $vehicles = Vehicle::all();

    if ($users->isEmpty() || $vehicles->isEmpty()) {
      return;
    }

    for ($i = 0; $i < 100; $i++) {
      $requestedAt = Carbon::now()->subDays(rand(1, 60));
      $status = $statuses[array_rand($statuses)];

      $data = [
        'vehicle_id' => $vehicles->random()->id,
        'user_id' => $users->random()->id,
        'maintenance_type' => $maintenanceTypes[array_rand($maintenanceTypes)],
        'description' => $descriptions[array_rand($descriptions)],
        'status' => $status,
        'requested_at' => $requestedAt,
        'notes' => rand(0, 1) ? 'Please check all systems thoroughly' : null,
      ];

      // Add status-specific dates and approver
      if ($status !== 'pending') {
        $data['approved_at'] = $requestedAt->copy()->addHours(rand(1, 24));
        $data['approved_by'] = $users->where('role', 'admin')->random()->id;

        if ($status === 'completed') {
          $data['completed_at'] = $data['approved_at']->copy()->addDays(rand(1, 7));
        }
      }

      MaintenanceRequest::create($data);
    }
  }
}
