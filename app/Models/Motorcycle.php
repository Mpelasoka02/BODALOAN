<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motorcycle extends Model
{
    protected $fillable = [
        'owner_id',
        'driver_id',
        'plate_number',
        'model',
        'make',
        'year',
        'color',
        'engine_cc',
        'engine_number',
        'chassis_number',
        'gps_device_id',
        'last_location_at',
        'registration_card',
        'insurance',
        'photo',
        'latitude',
        'longitude',
        'location_name',
        'weekly_amount',
        'loan_amount',
        'loan_duration_weeks',
        'status',
        'verification_status',
        'verification_notes',
        'stolen_at',
        'stolen_notes',
    ];

    protected $casts = [
        'last_location_at' => 'datetime',
        'stolen_at' => 'datetime',
        'weekly_amount' => 'decimal:2',
        'loan_amount' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function locations()
    {
        return $this->hasMany(VehicleLocation::class)->orderByDesc('recorded_at');
    }

    public function latestLocation()
    {
        return $this->hasOne(VehicleLocation::class)->latestOfMany('recorded_at');
    }

    public function hasGpsTracker(): bool
    {
        return !empty($this->gps_device_id);
    }

    public function gpsSignalStatus(): string
    {
        if (!$this->hasGpsTracker()) return 'none';
        if (!$this->last_location_at) return 'none';
        return $this->last_location_at->diffInHours(now()) < 24 ? 'live' : 'stale';
    }

    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function isPendingVerification()
    {
        return $this->verification_status === 'pending_verification';
    }

    public function hasActiveLoan()
    {
        return $this->loan()->whereIn('status', ['active', 'pending'])->exists();
    }

    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isAssigned()
    {
        return $this->status === 'assigned';
    }
}
