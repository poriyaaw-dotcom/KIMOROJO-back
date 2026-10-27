<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('accessories_making_charges', 5, 2)->default(0)->after('accessories_price');
            $table->decimal('accessories_profit', 5, 2)->default(0)->after('accessories_making_charges');
            $table->boolean('apply_tax_on_accessories')->default(false)->after('accessories_profit');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['accessories_making_charges', 'accessories_profit', 'apply_tax_on_accessories']);
        });
    }
};