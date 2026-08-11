<?php

namespace App\Models;

use App\Services\FirebaseService;
use App\Services\SmsService;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

    public static function createNotification($userId, $type, $title, $message, $data = null)
    {
        $notification = static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        static::sendSmsNotification($userId, $type, $message, $data);
        static::sendFirebasePush($userId, $type, $title, $message, $data);
        static::broadcastRealTime($userId, $notification);

        return $notification;
    }

    private static function sendSmsNotification(int $userId, string $type, string $message, ?array $data): void
    {
        $smsTypes = [
            'motorcycle_assigned',
            'account_approved',
            'account_suspended',
            'payment_submitted',
            'payment_verified',
            'payment_rejected',
            'loan_created',
            'loan_completed',
            'agreement_accepted',
        ];

        if (!in_array($type, $smsTypes)) {
            return;
        }

        $user = User::find($userId);
        if (!$user || empty($user->phone)) {
            return;
        }

        try {
            $sms = app(SmsService::class);
            $sms->send($user->phone, "BodaLink: {$message}");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SMS notification failed', [
                'user_id' => $userId,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private static function sendFirebasePush(int $userId, string $type, string $title, string $message, ?array $data): void
    {
        try {
            $firebase = app(FirebaseService::class);
            $firebase->sendPushToUser($userId, $title, $message, array_merge($data ?? [], [
                'type' => $type,
                'url' => static::getTypeUrl($type, $data),
            ]));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::channel('firebase')->error('Firebase push notification failed', [
                'user_id' => $userId,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private static function broadcastRealTime(int $userId, self $notification): void
    {
        try {
            $firebase = app(FirebaseService::class);
            $firebase->broadcastNotification($userId, [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'read' => false,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::channel('firebase')->error('Firebase realtime broadcast failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private static function getTypeUrl(?string $type, ?array $data): string
    {
        $routes = [
            'motorcycle_assigned' => '/motorcycles',
            'payment_submitted' => '/payments',
            'payment_verified' => '/payments',
            'payment_rejected' => '/payments',
            'loan_created' => '/loans',
            'loan_completed' => '/loans',
            'agreement_accepted' => '/loans',
            'account_approved' => '/dashboard',
            'account_suspended' => '/login',
        ];

        $route = $routes[$type] ?? '/dashboard';

        if (isset($data['loan_id'])) {
            $route = "/loans/{$data['loan_id']}";
        } elseif (isset($data['payment_id'])) {
            $route = "/payments";
        } elseif (isset($data['motorcycle_id'])) {
            $route = "/motorcycles/{$data['motorcycle_id']}";
        }

        return $route;
    }
}
