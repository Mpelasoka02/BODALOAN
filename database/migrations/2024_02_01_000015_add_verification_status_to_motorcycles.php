<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            if (!Schema::hasColumn('motorcycles', 'verification_status')) {
                $table->string('verification_status', 20)->default('verified')->after('status');
            }
            if (!Schema::hasColumn('motorcycles', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('verification_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'verification_notes']);
        });
    }
};
