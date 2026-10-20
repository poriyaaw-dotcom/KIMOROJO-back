<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing foreign key that has cascade delete
            $table->dropForeign(['address_id']);
            
            // Add it back with restrict (prevents deleting address if orders exist)
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
        });
    }
};