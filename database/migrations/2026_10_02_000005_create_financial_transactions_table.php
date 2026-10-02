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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ticket_id')->nullable()->constrained('service_tickets')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('type', 30)->index();
            $table->decimal('amount', 15, 2);
            $table->decimal('modal_amount', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->string('payment_method', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
