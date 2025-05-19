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
            $table->string('aw_product_id')->unique();
            $table->string('product_name')->nullable();
            $table->string('aw_deep_link')->nullable();
            $table->string('merchant_product_id')->nullable();
            $table->string('merchant_image_url')->nullable();
            $table->text('description')->nullable();
            $table->string('merchant_category')->nullable();
            $table->string('search_price')->nullable();
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->timestamps();
    
            // $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            // $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
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
