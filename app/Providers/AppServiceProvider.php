<?php

namespace App\Providers;

use App\Services\FirebaseService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsService::class, function () {
            return new SmsService();
        });

        $this->app->singleton(FirebaseService::class, function () {
            return new FirebaseService();
        });
    }

    public function boot(): void
    {
        Artisan::command('loans:check-overdue', function () {
            return app(\App\Console\Commands\CheckOverdueLoans::class)->handle();
        })->describe('Check and mark overdue/defaulted loans');
    }
}
