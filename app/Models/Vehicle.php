<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vin',
        'license_plate',
        'make',
        'model',
        'year',
        'color',
        'vehicle_type',
        'status',
        'current_mileage',
        'fuel_type',
        'transmission_type',
        'last_service_date',
        'next_service_due_date',
        'purchase_date',
        'purchase_price',
        'current_value',
        'insurance_provider',
        'insurance_policy_number',
        'insurance_expiry_date',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'current_mileage' => 'integer',
        'last_service_date' => 'date',
        'next_service_due_date' => 'date',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'insurance_expiry_date' => 'date',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrand::class, 'make', 'name');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'model', 'name');
    }
}
