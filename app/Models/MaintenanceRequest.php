<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'maintenance_type',
        'description',
        'status', // pending, approved, rejected, completed
        'requested_at',
        'approved_at',
        'completed_at',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Maintenance types
    const TYPE_OIL_CHANGE = 'oil_change';
    const TYPE_TIRE_CHANGE = 'tire_change';
    const TYPE_BRAKE_SERVICE = 'brake_service';
    const TYPE_ENGINE_REPAIR = 'engine_repair';
    const TYPE_TRANSMISSION = 'transmission';
    const TYPE_ELECTRICAL = 'electrical';
    const TYPE_AC_SERVICE = 'ac_service';
    const TYPE_GENERAL_CHECKUP = 'general_checkup';

    public static function getMaintenanceTypes(): array
    {
        return [
            self::TYPE_OIL_CHANGE => 'Oil Change',
            self::TYPE_TIRE_CHANGE => 'Tire Change',
            self::TYPE_BRAKE_SERVICE => 'Brake Service',
            self::TYPE_ENGINE_REPAIR => 'Engine Repair',
            self::TYPE_TRANSMISSION => 'Transmission Service',
            self::TYPE_ELECTRICAL => 'Electrical System',
            self::TYPE_AC_SERVICE => 'AC Service',
            self::TYPE_GENERAL_CHECKUP => 'General Checkup',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
