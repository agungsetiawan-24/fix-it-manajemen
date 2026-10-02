<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    /**
     * Get the service tickets for this customer.
     */
    public function serviceTickets(): HasMany
    {
        return $this->hasMany(ServiceTicket::class, 'customer_id');
    }

    /**
     * Get the latest service ticket for this customer.
     */
    public function latestTicket(): HasOne
    {
        return $this->hasOne(ServiceTicket::class, 'customer_id')->latestOfMany();
    }
}
