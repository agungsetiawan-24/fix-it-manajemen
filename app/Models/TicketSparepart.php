<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TicketSparepart extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_spareparts';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'sparepart_id',
        'quantity',
        'buy_price',
        'sell_price',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'buy_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
        ];
    }

    /**
     * Service ticket for this pivot record.
     */
    public function serviceTicket(): BelongsTo
    {
        return $this->belongsTo(ServiceTicket::class, 'ticket_id');
    }

    /**
     * Inventory sparepart for this pivot record.
     */
    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(InventorySparepart::class, 'sparepart_id');
    }
}
