<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nida', 30)->nullable()->unique()->after('phone');
            $table->string('profile_photo')->nullable()->after('nida');
            $table->string('id_photo')->nullable()->after('profile_photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nida', 'profile_photo', 'id_photo']);
        });
    }
};
