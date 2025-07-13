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
        'odometer_during_last_service',
        'estimated_next_service_odometer',
        'fuel_type',
        'transmission_type',
        'last_service_date',
        'next_service_due_date',
        'insurance_provider',
        'insurance_policy_number',
        'insurance_expiry_date',
        'notes',
        'last_registration_renewal',
        'next_registration_renewal',
        'renewal_fee',
    ];

    protected $casts = [
        'year' => 'integer',
        'current_mileage' => 'integer',
        'odometer_during_last_service' => 'integer',
        'estimated_next_service_odometer' => 'integer',
        'last_service_date' => 'date',
        'next_service_due_date' => 'date',
        'insurance_expiry_date' => 'date',
        'last_registration_renewal' => 'date',
        'next_registration_renewal' => 'date',
        'renewal_fee' => 'decimal:2',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrand::class, 'make', 'name');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'model', 'name');
    }

    /**
     * Get all maintenance requests for the vehicle.
     */
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'vehicle_id');
    }
}
