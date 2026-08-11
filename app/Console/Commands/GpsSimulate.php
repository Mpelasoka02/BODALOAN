<?php

namespace App\Console\Commands;

use App\Models\Motorcycle;
use App\Models\VehicleLocation;
use Illuminate\Console\Command;

class GpsSimulate extends Command
{
    protected $signature = 'gps:simulate {--count=3 : Number of vehicles to simulate} {--interval=5 : Seconds between updates}';
    protected $description = 'Generate fake GPS positions for demo vehicles (Dar es Salaam area)';

    public function handle(): int
    {
        $vehicles = Motorcycle::where('verification_status', 'verified')
            ->where('status', 'assigned')
            ->whereNotNull('driver_id')
            ->take((int) $this->option('count'))
            ->get();

        if ($vehicles->isEmpty()) {
            $vehicles = Motorcycle::where('verification_status', 'verified')
                ->take((int) $this->option('count'))
                ->get();
        }

        if ($vehicles->isEmpty()) {
            $this->error('No vehicles found. Run the seeder first.');
            return self::FAILURE;
        }

        $this->info("Simulating GPS for {$vehicles->count()} vehicles...");
        $this->info('Press Ctrl+C to stop.');

        $centers = [
            [-6.7924, 39.2083],
            [-6.1630, 35.7516],
            [-6.3690, 34.8888],
            [-6.7763, 39.2313],
            [-6.2220, 35.7470],
        ];

        $lat = [];
        $lng = [];
        foreach ($vehicles as $i => $v) {
            $center = $centers[$i % count($centers)];
            $lat[$i] = $center[0] + (mt_rand(-50, 50) / 1000);
            $lng[$i] = $center[1] + (mt_rand(-50, 50) / 1000);
        }

        while (true) {
            foreach ($vehicles as $i => $vehicle) {
                $lat[$i] += (mt_rand(-20, 20) / 10000);
                $lng[$i] += (mt_rand(-20, 20) / 10000);
                $speed = mt_rand(0, 80);
                $course = mt_rand(0, 359);

                VehicleLocation::create([
                    'motorcycle_id' => $vehicle->id,
                    'latitude' => $lat[$i],
                    'longitude' => $lng[$i],
                    'speed' => $speed,
                    'course' => $course,
                    'altitude' => mt_rand(50, 200),
                    'recorded_at' => now(),
                ]);

                $vehicle->update(['last_location_at' => now()]);

                $this->line("  <info>{$vehicle->plate_number}</info> → {$lat[$i]}, {$lng[$i]} @ {$speed} km/h");
            }

            $this->newLine();
            sleep((int) $this->option('interval'));
        }

        return self::SUCCESS;
    }
}
