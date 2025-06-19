<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleBrand extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'name',
    'description',
    'status',
  ];

  protected $casts = [
    'status' => 'boolean',
  ];

  public function vehicles(): HasMany
  {
    return $this->hasMany(Vehicle::class, 'make', 'name');
  }

  public function models(): HasMany
  {
    return $this->hasMany(VehicleModel::class);
  }
}
