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
        Schema::create('ticket_spareparts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ticket_id')->constrained('service_tickets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('sparepart_id')->constrained('inventory_spareparts')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('buy_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->string('status', 30)->default('approved'); // pending_approval, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_spareparts');
    }
};
