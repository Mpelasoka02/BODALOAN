<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'motorcycle_id',
        'owner_id',
        'driver_id',
        'total_amount',
        'weekly_installment',
        'amount_paid',
        'duration_weeks',
        'start_date',
        'end_date',
        'next_payment_date',
        'status',
        'agreement_accepted_at',
        'agreement_rejection_note',
        'ownership_certificate_generated',
        'absconded_at',
        'absconded_by',
        'absconded_reason',
        'recovered_at',
        'recovery_notes',
    ];

    protected $attributes = [
        'ownership_certificate_generated' => false,
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_payment_date' => 'date',
        'agreement_accepted_at' => 'datetime',
        'absconded_at' => 'datetime',
        'recovered_at' => 'datetime',
    ];

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function getBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->amount_paid);
    }

    public function getProgressAttribute()
    {
        if ($this->total_amount <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->amount_paid / $this->total_amount) * 100));
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isOverdue()
    {
        return $this->status === 'overdue';
    }

    public function isDefaulted()
    {
        return $this->status === 'defaulted';
    }

    public function isAgreementAccepted()
    {
        return $this->agreement_accepted_at !== null;
    }

    public static function driverHasActiveLoan($driverId): bool
    {
        return static::where('driver_id', $driverId)
            ->whereIn('status', ['pending', 'active', 'overdue'])
            ->exists();
    }
}
