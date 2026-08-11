<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('absconded_at')->nullable()->after('agreement_accepted_at');
            $table->unsignedBigInteger('absconded_by')->nullable()->after('absconded_at');
            $table->text('absconded_reason')->nullable()->after('absconded_by');
            $table->timestamp('recovered_at')->nullable()->after('absconded_reason');
            $table->text('recovery_notes')->nullable()->after('recovered_at');
        });

        Schema::table('motorcycles', function (Blueprint $table) {
            $table->timestamp('stolen_at')->nullable()->after('last_location_at');
            $table->text('stolen_notes')->nullable()->after('stolen_at');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['absconded_at', 'absconded_by', 'absconded_reason', 'recovered_at', 'recovery_notes']);
        });

        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn(['stolen_at', 'stolen_notes']);
        });
    }
};
