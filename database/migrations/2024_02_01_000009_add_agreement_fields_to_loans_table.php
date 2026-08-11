<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('agreement_accepted_at')->nullable()->after('status');
            $table->text('agreement_rejection_note')->nullable()->after('agreement_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['agreement_accepted_at', 'agreement_rejection_note']);
        });
    }
};
