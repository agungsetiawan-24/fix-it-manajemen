<?php

namespace App\Models;

use App\Enums\ChecklistCondition;
use Database\Factories\TicketChecklistFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketChecklist extends Model
{
    /** @use HasFactory<TicketChecklistFactory> */
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ticket_checklists';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_id',
        'item_name',
        'condition_before',
        'condition_after',
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
            'condition_before' => ChecklistCondition::class,
            'condition_after' => ChecklistCondition::class,
        ];
    }

    /**
     * The service ticket that this checklist item belongs to.
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
}
