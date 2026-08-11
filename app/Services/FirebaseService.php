<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    private string $projectId;
    private string $databaseUrl;
    private string $serviceAccountPath;
    private bool $fcmEnabled;
    private bool $realtimeDbEnabled;
    private ?string $cachedAccessToken = null;
    private string $caBundlePath;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id', '');
        $this->databaseUrl = config('firebase.database_url', '');
        $this->serviceAccountPath = config('firebase.service_account_path', '');
        $this->fcmEnabled = config('firebase.fcm_enabled', false);
        $this->realtimeDbEnabled = config('firebase.realtime_db_enabled', false);
        $this->caBundlePath = storage_path('app/cacert.pem');
    }

    private function httpPost(string $url, array $data, array $headers = [])
    {
        return Http::withOptions([
            'verify' => file_exists($this->caBundlePath) ? $this->caBundlePath : true,
        ])->withHeaders($headers)->post($url, $data);
    }

    private function httpPut(string $url, mixed $data, array $headers = [])
    {
        return Http::withOptions([
            'verify' => file_exists($this->caBundlePath) ? $this->caBundlePath : true,
        ])->withHeaders($headers)->put($url, $data);
    }

    private function httpGet(string $url, array $headers = [])
    {
        return Http::withOptions([
            'verify' => file_exists($this->caBundlePath) ? $this->caBundlePath : true,
        ])->withHeaders($headers)->get($url);
    }

    private function httpDelete(string $url, array $headers = [])
    {
        return Http::withOptions([
            'verify' => file_exists($this->caBundlePath) ? $this->caBundlePath : true,
        ])->withHeaders($headers)->delete($url);
    }

    // =============================================
    // FIREBASE CLOUD MESSAGING (FCM V1)
    // =============================================

    /**
     * Generate OAuth2 access token from service account
     */
    private function getAccessToken(): ?string
    {
        if ($this->cachedAccessToken) {
            return $this->cachedAccessToken;
        }

        $saPath = $this->serviceAccountPath;
        if (!file_exists($saPath)) {
            Log::channel('firebase')->error('Firebase service account file not found', ['path' => $saPath]);
            return null;
        }

        $serviceAccount = json_decode(file_get_contents($saPath), true);
        if (!$serviceAccount) {
            Log::channel('firebase')->error('Invalid Firebase service account JSON');
            return null;
        }

        $now = time();
        $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
        if (!$privateKey) {
            Log::channel('firebase')->error('Failed to read private key from service account');
            return null;
        }

        $header = $this->base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = $this->base64url(json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signatureInput = "$header.$claims";
        openssl_sign($signatureInput, $signature, $privateKey, 'SHA256');
        $jwt = "$signatureInput." . $this->base64url($signature);

        $response = Http::withOptions([
            'verify' => file_exists($this->caBundlePath) ? $this->caBundlePath : true,
        ])->asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->successful()) {
            $this->cachedAccessToken = $response->json('access_token');
            return $this->cachedAccessToken;
        }

        Log::channel('firebase')->error('Failed to get Firebase access token', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        return null;
    }

    private function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Send push notification to a single device token (FCM V1)
     */
    public function sendPushNotification(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->fcmEnabled) {
            Log::channel('firebase')->info('FCM V1 push would be sent (disabled)', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'body' => $body,
            ]);
            return true;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return false;
        }

        try {
            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'webpush' => [
                        'fcm_options' => [
                            'link' => config('app.url'),
                        ],
                        'headers' => [
                            'TTL' => '86400',
                        ],
                    ],
                    'data' => array_merge($data, [
                        'title' => $title,
                        'body' => $body,
                        'timestamp' => now()->toIso8601String(),
                    ]),
                ],
            ];

            $response = $this->httpPost("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $message, [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ]);

            if ($response->successful()) {
                Log::channel('firebase')->info('FCM V1 push sent successfully', [
                    'token' => substr($token, 0, 20) . '...',
                    'title' => $title,
                ]);
                return true;
            }

            Log::channel('firebase')->error('FCM V1 push failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::channel('firebase')->error('FCM V1 push exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send push notification to a user (looks up their tokens)
     */
    public function sendPushToUser(int $userId, string $title, string $body, array $data = []): bool
    {
        if (!$this->fcmEnabled) {
            return true;
        }

        $tokens = $this->getUserTokens($userId);
        if (empty($tokens)) {
            return true;
        }

        $success = true;
        foreach ($tokens as $token) {
            if (!$this->sendPushNotification($token, $title, $body, $data)) {
                $success = false;
            }
        }

        return $success;
    }

    // =============================================
    // FIREBASE REALTIME DATABASE
    // =============================================

    /**
     * Write data to Realtime Database
     */
    public function dbSet(string $path, mixed $data): bool
    {
        if (!$this->realtimeDbEnabled || empty($this->databaseUrl)) {
            Log::channel('firebase')->info('RTDB write would happen (disabled)', [
                'path' => $path,
            ]);
            return true;
        }

        try {
            $url = rtrim($this->databaseUrl, '/') . '/' . ltrim($path, '/') . '.json';
            $response = $this->httpPut($url, $data);

            if ($response->successful()) {
                return true;
            }

            Log::channel('firebase')->error('RTDB write failed', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::channel('firebase')->error('RTDB write exception', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Push data to an array node in Realtime Database
     */
    public function dbPush(string $path, mixed $data): ?string
    {
        if (!$this->realtimeDbEnabled || empty($this->databaseUrl)) {
            return null;
        }

        try {
            $url = rtrim($this->databaseUrl, '/') . '/' . ltrim($path, '/') . '.json';
            $response = $this->httpPost($url, $data);

            if ($response->successful()) {
                $result = $response->json();
                return $result['name'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::channel('firebase')->error('RTDB push exception', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Read data from Realtime Database
     */
    public function dbGet(string $path): ?array
    {
        if (!$this->realtimeDbEnabled || empty($this->databaseUrl)) {
            return null;
        }

        try {
            $url = rtrim($this->databaseUrl, '/') . '/' . ltrim($path, '/') . '.json';
            $response = $this->httpGet($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::channel('firebase')->error('RTDB read exception', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Delete data from Realtime Database
     */
    public function dbDelete(string $path): bool
    {
        if (!$this->realtimeDbEnabled || empty($this->databaseUrl)) {
            return true;
        }

        try {
            $url = rtrim($this->databaseUrl, '/') . '/' . ltrim($path, '/') . '.json';
            $response = $this->httpDelete($url);
            return $response->successful();

        } catch (\Exception $e) {
            Log::channel('firebase')->error('RTDB delete exception', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    // =============================================
    // REAL-TIME HELPERS
    // =============================================

    public function broadcastPaymentUpdate(array $paymentData): bool
    {
        $path = "payments/{$paymentData['id']}";
        return $this->dbSet($path, array_merge($paymentData, [
            'updated_at' => now()->toIso8601String(),
        ]));
    }

    public function broadcastLoanUpdate(array $loanData): bool
    {
        $path = "loans/{$loanData['id']}";
        return $this->dbSet($path, array_merge($loanData, [
            'updated_at' => now()->toIso8601String(),
        ]));
    }

    public function broadcastNotification(int $userId, array $notificationData): bool
    {
        $path = "users/{$userId}/notifications";
        return $this->dbPush($path, array_merge($notificationData, [
            'created_at' => now()->toIso8601String(),
        ])) !== null;
    }

    // =============================================
    // DEVICE TOKEN MANAGEMENT
    // =============================================

    public function registerDeviceToken(int $userId, string $token, string $platform = 'web'): bool
    {
        return $this->dbSet("device_tokens/{$userId}/{$token}", [
            'platform' => $platform,
            'created_at' => now()->toIso8601String(),
        ]);
    }

    public function removeDeviceToken(int $userId, string $token): bool
    {
        return $this->dbDelete("device_tokens/{$userId}/{$token}");
    }

    public function getUserTokens(int $userId): array
    {
        $tokens = $this->dbGet("device_tokens/{$userId}");

        if (!is_array($tokens)) {
            return [];
        }

        return array_keys($tokens);
    }

    public function getClientConfig(): array
    {
        return [
            'apiKey' => config('firebase.api_key', ''),
            'authDomain' => $this->projectId . '.firebaseapp.com',
            'databaseURL' => $this->databaseUrl,
            'projectId' => $this->projectId,
            'messagingSenderId' => config('firebase.fcm.sender_id', ''),
            'appId' => env('FIREBASE_APP_ID', ''),
        ];
    }
}
