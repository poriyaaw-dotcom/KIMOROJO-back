<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_fa');
            $table->text('description_en')->nullable();
            $table->text('description_fa')->nullable();
            
            // STRICT REQUIREMENTS FOR DYNAMIC PRICING
            $table->decimal('weight', 8, 2); // Weight in grams
            $table->string('purity');        // e.g., '18k'
            $table->decimal('making_charges', 10, 2); // Making charges
            
            $table->integer('stock')->default(0);
            $table->json('images')->nullable(); // Array of image URLs
            
            // Foreign Keys (Links to Category and Collection)
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};