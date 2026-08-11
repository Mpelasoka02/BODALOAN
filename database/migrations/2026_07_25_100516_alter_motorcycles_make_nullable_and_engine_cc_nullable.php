<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->string('model')->nullable()->change();
            $table->string('color')->nullable()->change();
            $table->string('engine_cc')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('motorcycles', function (Blueprint $table) {
            $table->string('model')->nullable(false)->change();
            $table->string('color')->nullable(false)->change();
            $table->string('engine_cc')->nullable(false)->change();
        });
    }
};
