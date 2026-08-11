<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner() || $user->isDriver();
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $loan = $payment->loan;

        if ($user->isOwner() && $loan && $loan->owner_id === $user->id) {
            return true;
        }

        if ($user->isDriver() && $loan && $loan->driver_id === $user->id) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->isDriver()) {
            return $user->loans()->where('status', 'active')->exists()
                || $user->loans()->where('status', 'overdue')->exists();
        }

        if ($user->isOwner()) {
            return true;
        }

        return false;
    }

    public function verify(User $user, Payment $payment): bool
    {
        $loan = $payment->loan;

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOwner() && $loan && $loan->owner_id === $user->id) {
            return true;
        }

        return false;
    }

    public function reject(User $user, Payment $payment): bool
    {
        $loan = $payment->loan;

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isOwner() && $loan && $loan->owner_id === $user->id) {
            return true;
        }

        return false;
    }
}
