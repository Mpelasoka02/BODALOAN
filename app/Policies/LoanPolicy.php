<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner() || $user->isDriver();
    }

    public function view(User $user, Loan $loan): bool
    {
        return $user->isAdmin()
            || $loan->owner_id === $user->id
            || $loan->driver_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }

    public function accept(User $user, Loan $loan): bool
    {
        return $user->isDriver() && $loan->driver_id === $user->id;
    }

    public function complete(User $user, Loan $loan): bool
    {
        return $user->isAdmin() || ($user->isOwner() && $loan->owner_id === $user->id);
    }

    public function viewCertificate(User $user, Loan $loan): bool
    {
        return $user->isAdmin()
            || $loan->owner_id === $user->id
            || $loan->driver_id === $user->id;
    }
}
