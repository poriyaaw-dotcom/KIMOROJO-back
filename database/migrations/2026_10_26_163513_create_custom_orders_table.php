<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Linked to user if logged in
            $table->string('jewelry_type'); // ring, necklace, bracelet, earring, other
            $table->string('metal_type'); // gold_18k, silver_925
            $table->string('reference_image')->nullable();
            $table->text('details'); // model, stone, carving, etc.
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('status')->default('pending'); // pending, reviewing, approved, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_orders');
    }
};