<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Only add if it doesn't already exist
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('making_charges');
            }
            if (!Schema::hasColumn('products', 'accessories_price')) {
                $table->decimal('accessories_price', 12, 2)->default(0)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'stock')) {
                $table->dropColumn('stock');
            }
            if (Schema::hasColumn('products', 'accessories_price')) {
                $table->dropColumn('accessories_price');
            }
        });
    }
};