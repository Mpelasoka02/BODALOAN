<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->string('pdf_path')->nullable();
            $table->string('owner_signed_pdf')->nullable();
            $table->string('driver_signed_pdf')->nullable();
            $table->timestamp('owner_signed_at')->nullable();
            $table->timestamp('driver_signed_at')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
