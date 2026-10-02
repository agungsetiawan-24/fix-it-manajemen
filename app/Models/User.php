<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Check if user is administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if user is technician.
     */
    public function isTeknisi(): bool
    {
        return $this->role === UserRole::Teknisi;
    }

    /**
     * Scope query to only include technicians.
     */
    public function scopeTechnicians(Builder $query): Builder
    {
        return $query->where('role', UserRole::Teknisi);
    }

    /**
     * Scope query to only include admins.
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', UserRole::Admin);
    }

    /**
     * Service tickets assigned to this technician.
     */
    public function assignedTickets(): HasMany
    {
        return $this->hasMany(ServiceTicket::class, 'technician_id');
    }

    /**
     * Financial transactions handled/recorded by this user.
     */
    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'user_id');
    }
}
