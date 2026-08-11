<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->string('gps_device_id')->nullable()->after('chassis_number');
            $table->timestamp('last_location_at')->nullable()->after('gps_device_id');
        });

        Schema::create('vehicle_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('course', 6, 2)->nullable();
            $table->decimal('altitude', 8, 2)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['motorcycle_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_locations');
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn(['gps_device_id', 'last_location_at']);
        });
    }
};
