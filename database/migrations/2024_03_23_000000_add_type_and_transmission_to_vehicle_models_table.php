<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('vehicle_models', function (Blueprint $table) {
      $table->string('vehicle_type')->after('years')->comment('Type of vehicle (e.g., Motorcycle, Sedan, SUV, etc.)');
      $table->string('transmission_type')->after('vehicle_type')->comment('Type of transmission (e.g., Manual, Automatic, Semi-automatic, etc.)');
    });
  }

  public function down(): void
  {
    Schema::table('vehicle_models', function (Blueprint $table) {
      $table->dropColumn(['vehicle_type', 'transmission_type']);
    });
  }
};
