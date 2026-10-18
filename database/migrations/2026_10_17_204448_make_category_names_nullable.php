<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // This makes the English name optional so it won't crash if you only type Persian
            $table->string('name_en')->nullable()->change();
            $table->string('name_fa')->nullable()->change();
            $table->string('slug')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_en')->nullable(false)->change();
            $table->string('name_fa')->nullable(false)->change();
            $table->string('slug')->nullable(false)->change();
        });
    }
};