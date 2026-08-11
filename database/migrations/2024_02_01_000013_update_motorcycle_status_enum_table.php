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
            DB::statement("ALTER TABLE motorcycles DROP CONSTRAINT motorcycles_status_check");
            DB::statement("ALTER TABLE motorcycles ADD CONSTRAINT motorcycles_status_check CHECK (status IN ('available', 'assigned', 'completed', 'suspended', 'active', 'overdue', 'inactive'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE motorcycles DROP CONSTRAINT motorcycles_status_check");
            DB::statement("ALTER TABLE motorcycles ADD CONSTRAINT motorcycles_status_check CHECK (status IN ('active', 'completed', 'overdue', 'inactive'))");
        }
    }
};
