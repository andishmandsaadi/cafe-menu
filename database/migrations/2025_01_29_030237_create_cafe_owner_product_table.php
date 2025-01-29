<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cafe_owner_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cafe_owner_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id'); // To track which category the product belongs to
            $table->decimal('price', 8, 2); // Custom price for the product
            $table->timestamps();

            // Foreign keys
            $table->foreign('cafe_owner_id')->references('id')->on('cafe_owners')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            // Unique constraint to prevent duplicate entries
            $table->unique(['cafe_owner_id', 'product_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafe_owner_product');
    }
};
