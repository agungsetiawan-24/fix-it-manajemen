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
        Schema::create('service_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_code')->unique();
            $table->foreignUuid('customer_id')->constrained('customers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignUuid('technician_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('device_brand');
            $table->string('device_model');
            $table->string('device_imei')->nullable();
            $table->string('device_color')->nullable();
            $table->text('encrypted_device_pin')->nullable();
            $table->text('complaint_notes')->nullable();
            $table->text('technician_notes')->nullable();
            $table->string('status', 30)->default('antrian')->index();
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->integer('warranty_days')->default(0);
            $table->date('warranty_expiry_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_tickets');
    }
};
