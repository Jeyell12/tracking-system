<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('vehicle_models', function (Blueprint $table) {
      $table->id();
      $table->foreignId('vehicle_brand_id')->constrained()->cascadeOnDelete();
      $table->string('name');
      $table->json('years');
      $table->boolean('status')->default(true);
      $table->timestamps();
      $table->softDeletes();

      // Ensure model name is unique within a brand
      $table->unique(['vehicle_brand_id', 'name']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('vehicle_models');
  }
};
