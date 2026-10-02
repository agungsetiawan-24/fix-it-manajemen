<?php

namespace App\Models;

use Database\Factories\InventorySparepartFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InventorySparepart extends Model
{
    /** @use HasFactory<InventorySparepartFactory> */
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventory_spareparts';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'part_name',
        'part_code',
        'category',
        'stock',
        'min_stock',
        'buy_price',
        'sell_price',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'min_stock' => 'integer',
            'buy_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
        ];
    }

    /**
     * Check if sparepart stock is running low.
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Scope query for items with low stock.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    /**
     * Scope query by category.
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Service tickets that use this sparepart.
     */
    public function serviceTickets(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceTicket::class,
            'ticket_spareparts',
            'sparepart_id',
            'ticket_id'
        )
            ->using(TicketSparepart::class)
            ->withPivot(['id', 'quantity', 'buy_price', 'sell_price', 'status'])
            ->withTimestamps();
    }
}
