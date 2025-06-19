<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleModel extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'vehicle_brand_id',
    'name',
    'years',
    'vehicle_type',
    'transmission_type',
    'status',
  ];

  protected $casts = [
    'years' => 'array',
    'status' => 'boolean',
  ];

  public static function getVehicleTypes(): array
  {
    return [
      'Motorcycle' => 'Motorcycle',
      'Sedan' => 'Sedan',
      'SUV' => 'SUV',
      'Pickup' => 'Pickup',
      'Van' => 'Van',
      'Truck' => 'Truck',
      'Bus' => 'Bus',
      'Sports Car' => 'Sports Car',
      'Hatchback' => 'Hatchback',
      'Crossover' => 'Crossover',
      'Wagon' => 'Wagon',
      'Coupe' => 'Coupe',
      'Convertible' => 'Convertible',
    ];
  }

  public static function getTransmissionTypes(): array
  {
    return [
      'Manual' => 'Manual',
      'Automatic' => 'Automatic',
      'Semi-automatic' => 'Semi-automatic',
      'CVT' => 'CVT (Continuously Variable Transmission)',
      'DCT' => 'DCT (Dual-Clutch Transmission)',
      'AMT' => 'AMT (Automated Manual Transmission)',
    ];
  }

  public function brand(): BelongsTo
  {
    return $this->belongsTo(VehicleBrand::class, 'vehicle_brand_id');
  }

  public function vehicles()
  {
    return $this->hasMany(Vehicle::class, 'model', 'name');
  }
}
