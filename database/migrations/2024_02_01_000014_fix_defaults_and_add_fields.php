<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE payments DROP CONSTRAINT payments_status_check");
            DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('verified', 'pending', 'pending_verification', 'rejected'))");
            Schema::table('payments', function (Blueprint $table) {
                $table->string('status', 20)->default('pending_verification')->change();
            });

            DB::statement("ALTER TABLE loans DROP CONSTRAINT loans_status_check");
            DB::statement("ALTER TABLE loans ADD CONSTRAINT loans_status_check CHECK (status IN ('pending', 'active', 'completed', 'overdue', 'defaulted'))");
            Schema::table('loans', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->change();
                $table->boolean('ownership_certificate_generated')->default(false);
            });
        } else {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('status', 20)->default('pending_verification')->change();
            });
            Schema::table('loans', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->change();
                $table->boolean('ownership_certificate_generated')->default(false);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('search_index')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('status', 20)->default('verified')->change();
        });
        DB::statement("ALTER TABLE payments DROP CONSTRAINT payments_status_check");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('verified', 'pending', 'rejected'))");

        Schema::table('loans', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->change();
            $table->dropColumn('ownership_certificate_generated');
        });
        DB::statement("ALTER TABLE loans DROP CONSTRAINT loans_status_check");
        DB::statement("ALTER TABLE loans ADD CONSTRAINT loans_status_check CHECK (status IN ('active', 'completed', 'overdue', 'defaulted'))");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('search_index');
        });
    }
};
