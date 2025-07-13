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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->date('last_registration_renewal')->nullable();
            $table->date('next_registration_renewal')->nullable();
            $table->decimal('renewal_fee', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'last_registration_renewal',
                'next_registration_renewal',
                'renewal_fee'
            ]);
        });
    }
};
