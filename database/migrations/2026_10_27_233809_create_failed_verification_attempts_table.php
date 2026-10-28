<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('failed_verification_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('ip_address')->nullable();
            $table->timestamps(); // Fix #2: Use timestamps() instead of manual created_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_verification_attempts');
    }
};