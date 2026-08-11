<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    protected $signature = 'loans:send-reminders';

    protected $description = 'Send payment due-soon reminders to drivers (2 days before next payment)';

    public function handle(): int
    {
        $dueDate = now()->addDays(2)->toDateString();

        $loans = Loan::where('status', 'active')
            ->whereNotNull('next_payment_date')
            ->where('next_payment_date', '<=', $dueDate)
            ->where('next_payment_date', '>=', now()->toDateString())
            ->with('driver', 'motorcycle')
            ->get();

        $sentCount = 0;

        foreach ($loans as $loan) {
            $driver = $loan->driver;
            if (!$driver) {
                continue;
            }

            $plateNumber = $loan->motorcycle->plate_number ?? 'N/A';
            $daysUntil = (int) now()->diffInDays($loan->next_payment_date);
            $dayLabel = $daysUntil === 0 ? 'today' : ($daysUntil === 1 ? 'tomorrow' : "in {$daysUntil} days");

            $message = "Dear {$driver->name}, your weekly payment of TZS " . number_format($loan->weekly_installment) . " for bodaboda {$plateNumber} is due {$dayLabel}. Please make your payment to avoid overdue charges.";

            UserNotification::createNotification(
                $driver->id,
                'payment_reminder',
                'Payment Reminder',
                $message,
                ['loan_id' => $loan->id, 'motorcycle_plate' => $plateNumber]
            );

            if ($driver->phone) {
                try {
                    $sms = app(SmsService::class);
                    $sms->send($driver->phone, "BodaLink Reminder: {$message}");
                } catch (\Exception $e) {
                    Log::error('Failed to send payment reminder SMS', [
                        'loan_id' => $loan->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $sentCount++;
        }

        $this->info("Sent {$sentCount} payment reminders for loans due within 2 days.");

        $overdueLoans = Loan::whereIn('status', ['overdue', 'active'])
            ->whereNotNull('next_payment_date')
            ->where('next_payment_date', '<', now()->toDateString())
            ->with('driver', 'motorcycle')
            ->get();

        $overdueCount = 0;

        foreach ($overdueLoans as $loan) {
            $driver = $loan->driver;
            if (!$driver) {
                continue;
            }

            $plateNumber = $loan->motorcycle->plate_number ?? 'N/A';
            $daysOverdue = now()->diffInDays($loan->next_payment_date);
            $dayWord = $daysOverdue === 1 ? 'day' : 'days';

            $message = "Dear {$driver->name}, your payment of TZS " . number_format($loan->weekly_installment) . " for bodaboda {$plateNumber} is {$daysOverdue} {$dayWord} overdue (due: {$loan->next_payment_date->format('M d')}). Please pay TZS " . number_format($loan->balance) . " remaining immediately to avoid penalties.";

            UserNotification::createNotification(
                $driver->id,
                'payment_overdue_reminder',
                'Overdue Payment Reminder',
                $message,
                ['loan_id' => $loan->id, 'motorcycle_plate' => $plateNumber]
            );

            if ($driver->phone) {
                try {
                    $sms = app(SmsService::class);
                    $sms->send($driver->phone, "BodaLink: {$message}");
                } catch (\Exception $e) {
                    Log::error('Failed to send overdue reminder SMS', [
                        'loan_id' => $loan->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $overdueCount++;
        }

        $this->info("Sent {$overdueCount} overdue payment reminders.");

        return Command::SUCCESS;
    }
}
