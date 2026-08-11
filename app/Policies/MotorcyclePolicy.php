<?php

namespace App\Policies;

use App\Models\Motorcycle;
use App\Models\User;

class MotorcyclePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function view(User $user, Motorcycle $motorcycle): bool
    {
        return $user->isAdmin() || $motorcycle->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isOwner();
    }

    public function update(User $user, Motorcycle $motorcycle): bool
    {
        return $user->isAdmin() || $motorcycle->owner_id === $user->id;
    }

    public function delete(User $user, Motorcycle $motorcycle): bool
    {
        return $user->isAdmin() || $motorcycle->owner_id === $user->id;
    }

    public function assign(User $user, Motorcycle $motorcycle): bool
    {
        return $user->isAdmin() || $motorcycle->owner_id === $user->id;
    }
}
