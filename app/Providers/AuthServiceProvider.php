<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\Motorcycle::class => \App\Policies\MotorcyclePolicy::class,
        \App\Models\Loan::class => \App\Policies\LoanPolicy::class,
        \App\Models\Payment::class => \App\Policies\PaymentPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\UserNotification::class => \App\Policies\NotificationPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
