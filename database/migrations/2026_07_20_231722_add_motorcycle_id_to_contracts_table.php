<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('contracts', function (Blueprint $table) {
                $table->foreignId('motorcycle_id')->nullable()->constrained('motorcycles')->cascadeOnDelete();
            });
        } else {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropForeign('contracts_loan_id_foreign');
            });

            Schema::table('contracts', function (Blueprint $table) {
                $table->foreignId('motorcycle_id')->nullable()->constrained('motorcycles')->cascadeOnDelete();
            });

            DB::statement('ALTER TABLE contracts ALTER COLUMN loan_id DROP NOT NULL');

            Schema::table('contracts', function (Blueprint $table) {
                $table->foreign('loan_id')->references('id')->on('loans')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('motorcycle_id');
        });
    }
};
