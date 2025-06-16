<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('maintenance_requests', function (Blueprint $table) {
      $table->id();
      $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->string('maintenance_type');
      $table->text('description');
      $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
      $table->timestamp('requested_at')->useCurrent();
      $table->timestamp('approved_at')->nullable();
      $table->timestamp('completed_at')->nullable();
      $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
      $table->text('notes')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('maintenance_requests');
  }
};
