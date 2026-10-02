<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Database\Factories\ServiceTicketFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceTicket extends Model
{
    /** @use HasFactory<ServiceTicketFactory> */
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_code',
        'customer_id',
        'technician_id',
        'device_brand',
        'device_model',
        'device_imei',
        'device_color',
        'encrypted_device_pin',
        'complaint_notes',
        'technician_notes',
        'status',
        'total_cost',
        'warranty_days',
        'warranty_expiry_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'encrypted_device_pin' => 'encrypted',
            'total_cost' => 'decimal:2',
            'warranty_days' => 'integer',
            'warranty_expiry_date' => 'date',
        ];
    }

    /**
     * Customer who owns the device.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Technician assigned to service the device.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Intake and QC checklists for this ticket.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TicketChecklist::class, 'ticket_id');
    }

    /**
     * Financial transactions associated with this ticket.
     */
    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'ticket_id');
    }

    /**
     * Spareparts used for this ticket.
     */
    public function spareparts(): BelongsToMany
    {
        return $this->belongsToMany(
            InventorySparepart::class,
            'ticket_spareparts',
            'ticket_id',
            'sparepart_id'
        )
            ->using(TicketSparepart::class)
            ->withPivot(['id', 'quantity', 'buy_price', 'sell_price', 'status'])
            ->withTimestamps();
    }

    /**
     * Scope query to specific ticket status.
     */
    public function scopeStatus(Builder $query, TicketStatus|string $status): Builder
    {
        $value = $status instanceof TicketStatus ? $status->value : $status;

        return $query->where('status', $value);
    }

    /**
     * Scope query for tickets in progress.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            TicketStatus::Selesai->value,
            TicketStatus::Batal->value,
        ]);
    }

    /**
     * Scope query for tickets ready for customer pickup.
     */
    public function scopeReadyForPickup(Builder $query): Builder
    {
        return $query->where('status', TicketStatus::SiapDiambil->value);
    }

    /**
     * Check if warranty is currently valid.
     */
    public function isWarrantyActive(): bool
    {
        if (! $this->warranty_expiry_date) {
            return false;
        }

        return $this->warranty_expiry_date->isFuture() || $this->warranty_expiry_date->isToday();
    }
}
