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
        Schema::create('inventory_spareparts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('part_name');
            $table->string('part_code')->nullable()->unique();
            $table->string('category')->nullable()->index();
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->decimal('buy_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_spareparts');
    }
};
