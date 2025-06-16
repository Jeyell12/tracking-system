<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vin')->unique();
            $table->string('license_plate')->unique();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('color');
            $table->string('vehicle_type');
            $table->enum('status', ['active', 'maintenance', 'out_of_service'])->default('active');
            $table->integer('current_mileage');
            $table->string('fuel_type');
            $table->string('transmission_type');
            $table->date('last_service_date')->nullable();
            $table->date('next_service_due_date')->nullable();
            $table->date('purchase_date');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('current_value', 10, 2);
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
