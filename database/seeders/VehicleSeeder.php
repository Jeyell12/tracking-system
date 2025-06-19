<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VehicleSeeder extends Seeder
{
  public function run(): void
  {
    // Sample vehicle brands with their models and specifications
    $brandsData = [
      'Toyota' => [
        'Corolla' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Sedan',
          'transmission_type' => 'Automatic',
        ],
        'Camry' => [
          'years' => [2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Sedan',
          'transmission_type' => 'Automatic',
        ],
        'RAV4' => [
          'years' => [2020, 2021, 2022, 2023],
          'vehicle_type' => 'SUV',
          'transmission_type' => 'Automatic',
        ],
        'Hilux' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Pickup',
          'transmission_type' => 'Manual',
        ],
      ],
      'Honda' => [
        'Civic' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Sedan',
          'transmission_type' => 'CVT',
        ],
        'Accord' => [
          'years' => [2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Sedan',
          'transmission_type' => 'CVT',
        ],
        'CR-V' => [
          'years' => [2020, 2021, 2022, 2023],
          'vehicle_type' => 'SUV',
          'transmission_type' => 'CVT',
        ],
        'City' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Sedan',
          'transmission_type' => 'CVT',
        ],
      ],
      'Ford' => [
        'Ranger' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Pickup',
          'transmission_type' => 'Automatic',
        ],
        'Everest' => [
          'years' => [2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'SUV',
          'transmission_type' => 'Automatic',
        ],
        'Raptor' => [
          'years' => [2020, 2021, 2022, 2023],
          'vehicle_type' => 'Pickup',
          'transmission_type' => 'Automatic',
        ],
        'Territory' => [
          'years' => [2021, 2022, 2023],
          'vehicle_type' => 'SUV',
          'transmission_type' => 'DCT',
        ],
      ],
      'Mitsubishi' => [
        'Montero Sport' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'SUV',
          'transmission_type' => 'Automatic',
        ],
        'Strada' => [
          'years' => [2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Pickup',
          'transmission_type' => 'Automatic',
        ],
        'Xpander' => [
          'years' => [2020, 2021, 2022, 2023],
          'vehicle_type' => 'MPV',
          'transmission_type' => 'CVT',
        ],
        'Mirage' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Hatchback',
          'transmission_type' => 'CVT',
        ],
      ],
      'Nissan' => [
        'Navara' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Pickup',
          'transmission_type' => 'Manual',
        ],
        'Terra' => [
          'years' => [2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'SUV',
          'transmission_type' => 'Automatic',
        ],
        'Urvan' => [
          'years' => [2020, 2021, 2022, 2023],
          'vehicle_type' => 'Van',
          'transmission_type' => 'Manual',
        ],
        'Almera' => [
          'years' => [2018, 2019, 2020, 2021, 2022, 2023],
          'vehicle_type' => 'Sedan',
          'transmission_type' => 'CVT',
        ],
      ],
    ];

    // Create brands and their models
    foreach ($brandsData as $brandName => $models) {
      $brand = VehicleBrand::create([
        'name' => $brandName,
        'description' => "{$brandName} vehicles are known for their reliability and performance.",
        'status' => true,
      ]);

      foreach ($models as $modelName => $specs) {
        VehicleModel::create([
          'vehicle_brand_id' => $brand->id,
          'name' => $modelName,
          'years' => $specs['years'],
          'vehicle_type' => $specs['vehicle_type'],
          'transmission_type' => $specs['transmission_type'],
          'status' => true,
        ]);
      }
    }

    // Sample vehicle data
    $vehiclesData = [
      [
        'make' => 'Toyota',
        'model' => 'Corolla',
        'year' => 2022,
        'vin' => 'JTDKN3DU8D5',
        'license_plate' => 'ABC123',
        'color' => 'White',
        'vehicle_type' => 'Sedan',
        'status' => 'active',
        'current_mileage' => 15000,
        'fuel_type' => 'Gasoline',
        'transmission_type' => 'Automatic',
        'last_service_date' => Carbon::now()->subMonths(3),
        'next_service_due_date' => Carbon::now()->addMonths(3),
        'purchase_date' => Carbon::now()->subYear(),
        'purchase_price' => 25000,
        'current_value' => 22000,
        'insurance_provider' => 'State Farm',
        'insurance_policy_number' => 'POL-12345678',
        'insurance_expiry_date' => Carbon::now()->addYear(),
      ],
      [
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2021,
        'vin' => '2HGES1',
        'license_plate' => 'XYZ789',
        'color' => 'Black',
        'vehicle_type' => 'Sedan',
        'status' => 'active',
        'current_mileage' => 25000,
        'fuel_type' => 'Gasoline',
        'transmission_type' => 'Automatic',
        'last_service_date' => Carbon::now()->subMonths(2),
        'next_service_due_date' => Carbon::now()->addMonths(4),
        'purchase_date' => Carbon::now()->subYears(2),
        'purchase_price' => 23000,
        'current_value' => 19000,
        'insurance_provider' => 'Geico',
        'insurance_policy_number' => 'POL-87654321',
        'insurance_expiry_date' => Carbon::now()->addMonths(6),
      ],
      [
        'make' => 'Ford',
        'model' => 'Ranger',
        'year' => 2023,
        'vin' => 'MFBXX',
        'license_plate' => 'DEF456',
        'color' => 'Silver',
        'vehicle_type' => 'Pickup',
        'status' => 'active',
        'current_mileage' => 5000,
        'fuel_type' => 'Diesel',
        'transmission_type' => 'Automatic',
        'last_service_date' => Carbon::now()->subMonth(),
        'next_service_due_date' => Carbon::now()->addMonths(5),
        'purchase_date' => Carbon::now()->subMonths(6),
        'purchase_price' => 35000,
        'current_value' => 33000,
        'insurance_provider' => 'Progressive',
        'insurance_policy_number' => 'POL-24681357',
        'insurance_expiry_date' => Carbon::now()->addMonths(8),
      ],
      [
        'make' => 'Mitsubishi',
        'model' => 'Montero Sport',
        'year' => 2022,
        'vin' => 'MMFXX',
        'license_plate' => 'GHI789',
        'color' => 'Gray',
        'vehicle_type' => 'SUV',
        'status' => 'maintenance',
        'current_mileage' => 12000,
        'fuel_type' => 'Diesel',
        'transmission_type' => 'Automatic',
        'last_service_date' => Carbon::now()->subMonths(4),
        'next_service_due_date' => Carbon::now()->addMonths(2),
        'purchase_date' => Carbon::now()->subMonths(10),
        'purchase_price' => 32000,
        'current_value' => 28000,
        'insurance_provider' => 'Allstate',
        'insurance_policy_number' => 'POL-13579246',
        'insurance_expiry_date' => Carbon::now()->addMonths(10),
      ],
      [
        'make' => 'Nissan',
        'model' => 'Navara',
        'year' => 2021,
        'vin' => 'MNTXX',
        'license_plate' => 'JKL012',
        'color' => 'Blue',
        'vehicle_type' => 'Pickup',
        'status' => 'out_of_service',
        'current_mileage' => 18000,
        'fuel_type' => 'Diesel',
        'transmission_type' => 'Automatic',
        'last_service_date' => Carbon::now()->subMonths(5),
        'next_service_due_date' => Carbon::now()->addMonths(1),
        'purchase_date' => Carbon::now()->subMonths(15),
        'purchase_price' => 30000,
        'current_value' => 25000,
        'insurance_provider' => 'Liberty Mutual',
        'insurance_policy_number' => 'POL-98765432',
        'insurance_expiry_date' => Carbon::now()->addMonths(7),
      ],
    ];

    // Create sample vehicles
    foreach ($vehiclesData as $vehicleData) {
      Vehicle::create($vehicleData);
    }
  }
}
