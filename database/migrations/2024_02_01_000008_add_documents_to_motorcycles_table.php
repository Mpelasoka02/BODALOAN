<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->string('registration_card')->nullable()->after('engine_cc');
            $table->string('insurance')->nullable()->after('registration_card');
            $table->string('photo')->nullable()->after('insurance');
            $table->string('engine_number')->nullable()->after('photo');
            $table->string('chassis_number')->nullable()->after('engine_number');
            $table->decimal('weekly_amount', 12, 2)->nullable()->after('chassis_number');
            $table->decimal('loan_amount', 12, 2)->nullable()->after('weekly_amount');
            $table->integer('loan_duration_weeks')->nullable()->after('loan_amount');
            $table->string('enrollment_code', 20)->nullable()->after('loan_duration_weeks');
            $table->timestamp('enrollment_code_used_at')->nullable()->after('enrollment_code');
        });
    }

    public function down(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->dropColumn([
                'registration_card', 'insurance', 'photo',
                'engine_number', 'chassis_number',
                'weekly_amount', 'loan_amount', 'loan_duration_weeks',
                'enrollment_code', 'enrollment_code_used_at',
            ]);
        });
    }
};
