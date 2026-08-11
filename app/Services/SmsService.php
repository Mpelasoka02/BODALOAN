<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $apiKey;
    private string $username;
    private string $senderId;
    private string $environment;

    public function __construct()
    {
        $this->apiKey = config('sms.api_key', '');
        $this->username = config('sms.username', '');
        $this->senderId = config('sms.sender_id', 'BodaLink');
        $this->environment = config('sms.environment', 'sandbox');
    }

    public function send(string $to, string $message): bool
    {
        $phone = $this->formatPhone($to);

        if (empty($this->apiKey) || empty($this->username)) {
            Log::channel('sms')->info('SMS would be sent', [
                'to' => $phone,
                'message' => $message,
                'timestamp' => now()->toDateTimeString(),
            ]);
            return true;
        }

        try {
            $baseUrl = $this->environment === 'production'
                ? 'https://api.africastalking.com/version1/messaging'
                : 'https://api.sandbox.africastalking.com/version1/messaging';

            $response = Http::withOptions(['verify' => false])->withHeaders([
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])->asForm()->post($baseUrl, [
                'username' => $this->username,
                'to' => $phone,
                'message' => $message,
                'from' => $this->senderId,
            ]);

            if ($response->successful()) {
                Log::channel('sms')->info('SMS sent successfully', [
                    'to' => $phone,
                    'message' => $message,
                ]);
                return true;
            }

            Log::channel('sms')->error('SMS failed', [
                'to' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::channel('sms')->error('SMS exception', [
                'to' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendAccountApproved(string $phone, string $userName): bool
    {
        $message = "BodaLink: Hello {$userName}, your account has been approved! "
                 . "You can now log in and access the BodaLink system. "
                 . "Welcome aboard! - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendAccountSuspended(string $phone, string $userName, string $reason): bool
    {
        $message = "BodaLink: Hello {$userName}, your account has been suspended. "
                 . "Reason: {$reason}. "
                 . "Please contact support for assistance. - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendPaymentSubmitted(string $phone, string $driverName, float $amount, string $plateNumber): bool
    {
        $message = "BodaLink: Hi {$driverName}, your payment of TZS " . number_format($amount, 0, '.', ',')
                 . " for motorcycle {$plateNumber} has been submitted and is pending verification. - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendPaymentVerified(string $phone, string $driverName, float $amount, string $plateNumber): bool
    {
        $message = "BodaLink: Hi {$driverName}, your payment of TZS " . number_format($amount, 0, '.', ',')
                 . " for motorcycle {$plateNumber} has been verified. Thank you! - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendPaymentRejected(string $phone, string $driverName, float $amount, string $plateNumber, string $reason): bool
    {
        $message = "BodaLink: Hi {$driverName}, your payment of TZS " . number_format($amount, 0, '.', ',')
                 . " for motorcycle {$plateNumber} was rejected. Reason: {$reason}. - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendLoanCreated(string $phone, string $driverName, string $plateNumber): bool
    {
        $message = "BodaLink: Hi {$driverName}, a new loan agreement has been created for motorcycle {$plateNumber}. "
                 . "Please log in to review and accept the agreement. - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendLoanCompleted(string $phone, string $driverName, string $plateNumber): bool
    {
        $message = "BodaLink: Congratulations {$driverName}! Your loan for motorcycle {$plateNumber} "
                 . "has been fully paid. You now own this motorcycle! - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendAgreementAccepted(string $phone, string $ownerName, string $driverName, string $plateNumber): bool
    {
        $message = "BodaLink: Hi {$ownerName}, driver {$driverName} has accepted the loan agreement "
                 . "for motorcycle {$plateNumber}. The loan is now active. - BodaLink";

        return $this->send($phone, $message);
    }

    public function sendVerificationCode(string $phone, string $userName, string $code): bool
    {
        $message = "BodaLink: Karibu {$userName}! Your verification code is: {$code}. Enter this code to verify your account. - BodaLink";
        return $this->send($phone, $message);
    }

    private function formatPhone(string $phone): string
    {
        $phone = trim($phone);
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '+255' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '+255')) {
            $phone = '+255' . $phone;
        }

        return $phone;
    }
}
