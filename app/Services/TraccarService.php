<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TraccarService
{
    private string $baseUrl;
    private string $email;
    private string $password;
    private ?string $sessionCookie = null;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('traccar.url', ''), '/');
        $this->email = config('traccar.email', '');
        $this->password = config('traccar.password', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->email) && !empty($this->password);
    }

    private function authenticate(): ?string
    {
        if ($this->sessionCookie) {
            return $this->sessionCookie;
        }

        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->withQueryParameters([
                'email' => $this->email,
                'password' => $this->password,
            ])->get("{$this->baseUrl}/api/session");

            if ($response->successful()) {
                $this->sessionCookie = $response->header('Set-Cookie');
                if (!$this->sessionCookie) {
                    $this->sessionCookie = $response->cookies()->get('JSESSIONID') ?: 'connected';
                }
                return $this->sessionCookie;
            }
        } catch (\Exception $e) {
            Log::error('Traccar authentication failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function request(string $method, string $endpoint, array $data = []): ?array
    {
        $cookie = $this->authenticate();

        try {
            $http = Http::withOptions(['verify' => false]);

            if ($cookie && $cookie !== 'connected') {
                $http = $http->withHeaders(['Cookie' => $cookie]);
            }

            $url = "{$this->baseUrl}/api/{$endpoint}";

            $response = match(strtoupper($method)) {
                'GET' => $http->get($url, $data),
                'POST' => $http->post($url, $data),
                'PUT' => $http->put($url, $data),
                'DELETE' => $http->delete($url),
                default => $http->get($url, $data),
            };

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Traccar API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Traccar API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    // ── Devices ──

    public function getDevices(): array
    {
        return $this->request('GET', 'devices') ?? [];
    }

    public function registerDevice(string $imei, string $name): ?array
    {
        return $this->request('POST', 'devices', [
            'uniqueId' => $imei,
            'name' => $name,
        ]);
    }

    public function deleteDevice(int $deviceId): bool
    {
        $result = $this->request('DELETE', "devices/{$deviceId}");
        return $result !== null;
    }

    // ── Positions ──

    public function getPositions(int $deviceId): array
    {
        return $this->request('GET', "positions", ['deviceId' => $deviceId]) ?? [];
    }

    public function getLatestPosition(int $deviceId): ?array
    {
        $positions = $this->getPositions($deviceId);
        return is_array($positions) && count($positions) > 0
            ? (is_array($positions[0]) ? $positions[0] : $positions)
            : null;
    }

    public function getAllPositions(): array
    {
        return $this->request('GET', 'positions') ?? [];
    }

    // ── Route / History ──

    public function getRoute(int $deviceId, string $from, string $to): array
    {
        return $this->request('GET', 'reports/route', [
            'deviceId' => $deviceId,
            'from' => $from,
            'to' => $to,
        ]) ?? [];
    }
}
