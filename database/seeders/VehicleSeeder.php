<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VehicleSeeder extends Seeder
{
  public function run(): void
  {
    $makes = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes-Benz', 'Audi', 'Nissan', 'Hyundai', 'Kia'];
    $models = [
      'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Highlander', 'Tacoma'],
      'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Odyssey'],
      'Ford' => ['F-150', 'Mustang', 'Explorer', 'Escape', 'Ranger'],
      'Chevrolet' => ['Silverado', 'Malibu', 'Equinox', 'Tahoe', 'Colorado'],
      'BMW' => ['3 Series', '5 Series', 'X3', 'X5', 'M3'],
      'Mercedes-Benz' => ['C-Class', 'E-Class', 'GLC', 'GLE', 'S-Class'],
      'Audi' => ['A4', 'A6', 'Q5', 'Q7', 'TT'],
      'Nissan' => ['Altima', 'Rogue', 'Sentra', 'Pathfinder', 'Frontier'],
      'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Palisade'],
      'Kia' => ['Forte', 'Sorento', 'Sportage', 'Telluride', 'Stinger']
    ];
    $colors = ['Black', 'White', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Brown'];
    $vehicleTypes = ['Sedan', 'SUV', 'Truck', 'Van', 'Coupe', 'Hatchback'];
    $fuelTypes = ['Gasoline', 'Diesel', 'Electric', 'Hybrid'];
    $transmissionTypes = ['Automatic', 'Manual', 'CVT'];
    $insuranceProviders = ['State Farm', 'Geico', 'Progressive', 'Allstate', 'Liberty Mutual'];

    for ($i = 0; $i < 100; $i++) {
      $make = $makes[array_rand($makes)];
      $model = $models[$make][array_rand($models[$make])];
      $year = rand(2015, 2024);
      $purchaseDate = Carbon::now()->subDays(rand(1, 1000));
      $purchasePrice = rand(15000, 60000);
      $currentValue = $purchasePrice * (1 - (rand(5, 30) / 100));
      $currentMileage = rand(1000, 100000);
      $lastServiceDate = Carbon::now()->subDays(rand(1, 180));
      $nextServiceDueDate = $lastServiceDate->copy()->addDays(rand(30, 90));

      Vehicle::create([
        'vin' => strtoupper(substr(md5(rand()), 0, 17)),
        'license_plate' => strtoupper(substr(md5(rand()), 0, 7)),
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'color' => $colors[array_rand($colors)],
        'vehicle_type' => $vehicleTypes[array_rand($vehicleTypes)],
        'status' => rand(0, 1) ? 'active' : 'maintenance',
        'current_mileage' => $currentMileage,
        'fuel_type' => $fuelTypes[array_rand($fuelTypes)],
        'transmission_type' => $transmissionTypes[array_rand($transmissionTypes)],
        'last_service_date' => $lastServiceDate,
        'next_service_due_date' => $nextServiceDueDate,
        'purchase_date' => $purchaseDate,
        'purchase_price' => $purchasePrice,
        'current_value' => $currentValue,
        'insurance_provider' => $insuranceProviders[array_rand($insuranceProviders)],
        'insurance_policy_number' => 'POL-' . strtoupper(substr(md5(rand()), 0, 8)),
        'insurance_expiry_date' => Carbon::now()->addDays(rand(30, 365)),
        'notes' => rand(0, 1) ? 'Regular maintenance vehicle' : null,
      ]);
    }
  }
}
