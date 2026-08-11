<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserNotification;

class NotificationPolicy
{
    public function update(User $user, UserNotification $notification): bool
    {
        return $notification->user_id === $user->id;
    }

    public function markAllRead(User $user): bool
    {
        return true;
    }
}
