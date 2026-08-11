<?php

namespace App\Console\Commands;

use App\Models\Motorcycle;
use App\Models\VehicleLocation;
use App\Services\TraccarService;
use Illuminate\Console\Command;

class GpsSyncPositions extends Command
{
    protected $signature = 'gps:sync {--traccar : Pull positions from Traccar API instead of local table}';
    protected $description = 'Sync latest vehicle positions from Traccar into local cache';

    public function handle(TraccarService $traccar): int
    {
        $vehicles = Motorcycle::where('verification_status', 'verified')
            ->whereNotNull('gps_device_id')
            ->get();

        if ($vehicles->isEmpty()) {
            $this->info('No vehicles with GPS trackers assigned.');
            return self::SUCCESS;
        }

        if ($this->option('traccar') && $traccar->isConfigured()) {
            $this->info('Pulling positions from Traccar API...');
            $positions = $traccar->getAllPositions();

            foreach ($positions as $pos) {
                $deviceId = $pos['deviceId'] ?? null;
                if (!$deviceId) continue;

                $vehicle = $vehicles->firstWhere('gps_device_id', (string) $deviceId);
                if (!$vehicle) continue;

                $recordedAt = isset($pos['fixTime'])
                    ? \Carbon\Carbon::parse($pos['fixTime'])
                    : now();

                VehicleLocation::updateOrCreate(
                    [
                        'motorcycle_id' => $vehicle->id,
                        'recorded_at' => $recordedAt,
                    ],
                    [
                        'latitude' => $pos['latitude'] ?? 0,
                        'longitude' => $pos['longitude'] ?? 0,
                        'speed' => $pos['speed'] ?? null,
                        'course' => $pos['course'] ?? null,
                        'altitude' => $pos['altitude'] ?? null,
                    ]
                );

                $vehicle->update(['last_location_at' => $recordedAt]);
                $this->line("  <info>{$vehicle->plate_number}</info> → {$pos['latitude']}, {$pos['longitude']}");
            }
        } else {
            $this->info('Traccar not configured. Showing local cache status...');
            foreach ($vehicles as $v) {
                $last = $v->latestLocation;
                $status = $last ? $last->recorded_at->diffForHumans() : 'No data';
                $this->line("  {$v->plate_number}: {$status}");
            }
        }

        $this->info('Sync complete.');
        return self::SUCCESS;
    }
}
