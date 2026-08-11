<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\SmsService;
use Illuminate\Console\Command;

class CheckOverdueLoans extends Command
{
    protected $signature = 'loans:check-overdue';

    protected $description = 'Check and mark overdue/defaulted loans, notify drivers and admin';

    public function handle(): int
    {
        $now = now();
        $overdueThreshold = $now->copy()->subDays(3);
        $defaultedThreshold = $now->copy()->subDays(30);

        $loans = Loan::where('status', 'active')
            ->whereNotNull('next_payment_date')
            ->where('next_payment_date', '<', $overdueThreshold)
            ->get();

        $overdueCount = 0;
        $defaultedCount = 0;

        $admins = User::where('role', 'admin')->get();

        foreach ($loans as $loan) {
            $driver = $loan->driver;
            if (!$driver) {
                continue;
            }

            $motorcycle = $loan->motorcycle;
            $plateNumber = $motorcycle ? $motorcycle->plate_number : 'N/A';
            $weeksOverdue = $now->diffInWeeks($loan->next_payment_date);

            if ($loan->next_payment_date->lte($defaultedThreshold)) {
                $loan->update(['status' => 'defaulted']);
                $defaultedCount++;

                UserNotification::createNotification(
                    $driver->id,
                    'loan_defaulted',
                    'Loan Defaulted',
                    "Your loan for bodaboda {$plateNumber} has been marked as defaulted due to 30+ days of non-payment.",
                    ['loan_id' => $loan->id, 'motorcycle_plate' => $plateNumber]
                );

                foreach ($admins as $admin) {
                    UserNotification::createNotification(
                        $admin->id,
                        'loan_defaulted_admin',
                        'Loan Defaulted',
                        "Loan for {$driver->name} ({$plateNumber}) has been marked as defaulted. Outstanding: TZS " . number_format($loan->balance) . ".",
                        ['loan_id' => $loan->id]
                    );
                }

                $owner = $loan->owner;
                if ($owner) {
                    UserNotification::createNotification(
                        $owner->id,
                        'loan_defaulted_owner',
                        'Loan Defaulted',
                        "Driver {$driver->name} for {$plateNumber} has defaulted on their loan. Outstanding: TZS " . number_format($loan->balance) . ".",
                        ['loan_id' => $loan->id]
                    );
                }

                if ($driver->phone) {
                    try {
                        $sms = app(SmsService::class);
                        $sms->sendPaymentReminder($driver->phone, $driver->name, $plateNumber, 'defaulted');
                    } catch (\Exception $e) {
                        Log::error('Failed to send defaulted SMS', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);
                    }
                }
            } elseif ($loan->next_payment_date->lte($overdueThreshold)) {
                $loan->update(['status' => 'overdue']);
                $overdueCount++;

                UserNotification::createNotification(
                    $driver->id,
                    'loan_overdue',
                    'Loan Overdue',
                    "Your loan for bodaboda {$plateNumber} is {$weeksOverdue} week(s) overdue. Please make your payment of TZS " . number_format($loan->weekly_installment) . " immediately.",
                    ['loan_id' => $loan->id, 'motorcycle_plate' => $plateNumber]
                );

                foreach ($admins as $admin) {
                    UserNotification::createNotification(
                        $admin->id,
                        'loan_overdue_admin',
                        'Loan Overdue',
                        "Loan for {$driver->name} ({$plateNumber}) is {$weeksOverdue} week(s) overdue. Outstanding: TZS " . number_format($loan->balance) . ".",
                        ['loan_id' => $loan->id]
                    );
                }

                $owner = $loan->owner;
                if ($owner) {
                    UserNotification::createNotification(
                        $owner->id,
                        'loan_overdue_owner',
                        'Driver Payment Overdue',
                        "Driver {$driver->name} for {$plateNumber} is {$weeksOverdue} week(s) overdue. Outstanding: TZS " . number_format($loan->balance) . ".",
                        ['loan_id' => $loan->id]
                    );
                }

                if ($driver->phone) {
                    try {
                        $sms = app(SmsService::class);
                        $sms->sendPaymentReminder($driver->phone, $driver->name, $plateNumber, 'overdue');
                    } catch (\Exception $e) {
                        Log::error('Failed to send overdue SMS', ['loan_id' => $loan->id, 'error' => $e->getMessage()]);
                    }
                }
            }
        }

        $this->info("Processed {$loans->count()} loans: {$overdueCount} marked overdue, {$defaultedCount} marked defaulted.");

        return Command::SUCCESS;
    }
}
