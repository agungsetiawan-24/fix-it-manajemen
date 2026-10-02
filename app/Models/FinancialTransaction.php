<?php

namespace App\Models;

use App\Enums\TransactionType;
use Database\Factories\FinancialTransactionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    /** @use HasFactory<FinancialTransactionFactory> */
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'financial_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'type',
        'amount',
        'modal_amount',
        'net_profit',
        'payment_method',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'amount' => 'decimal:2',
            'modal_amount' => 'decimal:2',
            'net_profit' => 'decimal:2',
        ];
    }

    /**
     * The service ticket associated with this financial transaction (nullable).
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ServiceTicket::class, 'ticket_id');
    }

    /**
     * Alias for ticket relationship.
     */
    public function serviceTicket(): BelongsTo
    {
        return $this->ticket();
    }

    /**
     * The user / technician / admin who handled this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope query to specific transaction type.
     */
    public function scopeType(Builder $query, TransactionType|string $type): Builder
    {
        $value = $type instanceof TransactionType ? $type->value : $type;

        return $query->where('type', $value);
    }

    /**
     * Scope query for income transactions.
     */
    public function scopeIncome(Builder $query): Builder
    {
        return $query->whereIn('type', [
            TransactionType::PemasukanServis->value,
            TransactionType::PenjualanPos->value,
        ]);
    }

    /**
     * Scope query for expense transactions.
     */
    public function scopeExpense(Builder $query): Builder
    {
        return $query->whereIn('type', [
            TransactionType::PembelianSparepart->value,
            TransactionType::PengeluaranOps->value,
        ]);
    }
}
