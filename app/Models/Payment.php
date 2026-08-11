<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'loan_id',
        'amount',
        'payment_date',
        'method',
        'status',
        'notes',
        'reference_number',
        'receipt_path',
        'rejection_reason',
        'owner_notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function isPending()
    {
        return $this->status === 'pending_verification';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
