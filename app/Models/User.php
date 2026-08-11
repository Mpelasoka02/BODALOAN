<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'nida',
        'profile_photo',
        'id_photo',
        'address',
        'latitude',
        'longitude',
        'location_name',
        'birthdate',
        'role',
        'password',
        'approval_status',
        'rejection_reason',
        'is_active',
        'verification_submitted_at',
        'email_verified_at',
        'email_verification_code',
    ];

    protected $appends = [
        'profile_photo_url',
        'id_photo_url',
        'has_complete_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verification_code' => 'string',
        'password' => 'hashed',
        'birthdate' => 'date',
        'is_active' => 'boolean',
        'verification_submitted_at' => 'datetime',
    ];

    public function motorcycles()
    {
        return $this->hasMany(Motorcycle::class, 'owner_id');
    }

    public function assignedMotorcycle()
    {
        return $this->hasOne(Motorcycle::class, 'driver_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'driver_id');
    }

    public function ownerLoans()
    {
        return $this->hasMany(Loan::class, 'owner_id');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function locations()
    {
        return $this->hasMany(DriverLocation::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function unreadMessagesCount(): int
    {
        $userId = $this->id;

        if ($this->isAdmin()) {
            $loanUnread = Message::whereNotNull('loan_id')
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->count();

            $directUnread = Message::whereNull('loan_id')
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->count();

            return $loanUnread + $directUnread;
        }

        $loanIds = Loan::where(function ($q) use ($userId) {
            $q->where('owner_id', $userId)->orWhere('driver_id', $userId);
        })->pluck('id');

        $loanUnread = Message::whereNotNull('loan_id')
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->whereIn('loan_id', $loanIds)
            ->count();

        $directUnread = Message::whereNull('loan_id')
            ->where('receiver_id', $userId)
            ->whereNull('read_at')
            ->count();

        return $loanUnread + $directUnread;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isOwner()
    {
        return $this->role === 'owner';
    }

    public function isDriver()
    {
        return $this->role === 'driver';
    }

    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    public function isPending()
    {
        return $this->approval_status === 'pending';
    }

    public function isSuspended()
    {
        return $this->approval_status === 'suspended';
    }

    public function unreadNotificationsCount()
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? asset('storage/' . $this->profile_photo) : null;
    }

    public function getIdPhotoUrlAttribute()
    {
        return $this->id_photo ? asset('storage/' . $this->id_photo) : null;
    }

    public function getHasCompleteProfileAttribute()
    {
        return !empty($this->nida) && !empty($this->profile_photo) && !empty($this->id_photo);
    }

    public function needsProfileSetup()
    {
        if ($this->isAdmin()) {
            return false;
        }
        return empty($this->nida) || empty($this->profile_photo) || empty($this->id_photo);
    }

    public function hasSubmittedVerification()
    {
        return !is_null($this->verification_submitted_at);
    }

    public function hasVerificationDocuments()
    {
        return !empty($this->nida) && !empty($this->profile_photo) && !empty($this->id_photo);
    }
}
