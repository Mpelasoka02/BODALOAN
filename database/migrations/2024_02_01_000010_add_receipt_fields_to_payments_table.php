<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('reference_number')->nullable()->after('method');
            $table->string('receipt_path')->nullable()->after('reference_number');
            $table->text('rejection_reason')->nullable()->after('receipt_path');
            $table->text('owner_notes')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['reference_number', 'receipt_path', 'rejection_reason', 'owner_notes']);
        });
    }
};
