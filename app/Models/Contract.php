<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'loan_id',
        'motorcycle_id',
        'contract_number',
        'pdf_path',
        'owner_signed_pdf',
        'driver_signed_pdf',
        'owner_signed_at',
        'driver_signed_at',
        'owner_approved_at',
        'admin_approved_at',
        'admin_id',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'owner_signed_at' => 'datetime',
        'driver_signed_at' => 'datetime',
        'owner_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOwnerSigned()
    {
        return !is_null($this->owner_signed_at);
    }

    public function isDriverSigned()
    {
        return !is_null($this->driver_signed_at);
    }

    public function isFullySigned()
    {
        return $this->isOwnerSigned() && $this->isDriverSigned();
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isOwnerApproved()
    {
        return !is_null($this->owner_approved_at);
    }
}
