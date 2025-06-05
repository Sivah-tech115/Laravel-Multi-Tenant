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
            $table->id(); // Primary key
            $table->text('import_id')->nullable();
            $table->text('aw_deep_link')->nullable();
            $table->text('product_name')->nullable();
            $table->string('aw_product_id', 191)->unique();
            $table->text('merchant_product_id')->nullable();
            $table->text('merchant_image_url')->nullable();
            $table->text('description')->nullable();
            $table->text('slug')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keyword')->nullable();
            $table->text('merchant_category')->nullable();
            $table->text('merchant_category_slug')->nullable();
            $table->text('search_price')->nullable();
            $table->text('aw_image_url')->nullable();
            $table->text('currency')->nullable();
            $table->text('store_price')->nullable();
            $table->text('delivery_cost')->nullable();
            $table->text('merchant_deep_link')->nullable();
            $table->text('language')->nullable();
            $table->text('last_updated')->nullable();
            $table->text('display_price')->nullable();
            $table->text('data_feed_id')->nullable();
            $table->text('colour')->nullable();
            $table->text('product_short_description')->nullable();
            $table->text('specifications')->nullable();
            $table->text('condition')->nullable();
            $table->text('product_model')->nullable();
            $table->text('model_number')->nullable();
            $table->text('dimensions')->nullable();
            $table->text('keywords')->nullable();
            $table->text('promotional_text')->nullable();
            $table->text('product_type')->nullable();
            $table->text('commission_group')->nullable();
            $table->text('merchant_product_category_path')->nullable();
            $table->text('merchant_product_second_category')->nullable();
            $table->text('merchant_product_third_category')->nullable();
            $table->text('rrp_price')->nullable();
            $table->text('saving')->nullable();
            $table->text('savings_percent')->nullable();
            $table->text('base_price')->nullable();
            $table->text('base_price_amount')->nullable();
            $table->text('base_price_text')->nullable();
            $table->text('product_price_old')->nullable();
            $table->text('delivery_restrictions')->nullable();
            $table->text('delivery_weight')->nullable();
            $table->text('warranty')->nullable();
            $table->text('terms_of_contract')->nullable();
            $table->text('delivery_time')->nullable();
            $table->text('in_stock')->nullable();
            $table->text('stock_quantity')->nullable();
            $table->text('valid_from')->nullable();
            $table->text('valid_to')->nullable();
            $table->text('is_for_sale')->nullable();
            $table->text('web_offer')->nullable();
            $table->text('pre_order')->nullable();
            $table->text('stock_status')->nullable();
            $table->text('size_stock_status')->nullable();
            $table->text('size_stock_amount')->nullable();
            $table->text('merchant_thumb_url')->nullable();
            $table->text('large_image')->nullable();
            $table->text('alternate_image')->nullable();
            $table->text('aw_thumb_url')->nullable();
            $table->text('alternate_image_two')->nullable();
            $table->text('alternate_image_three')->nullable();
            $table->text('alternate_image_four')->nullable();
            $table->text('reviews')->nullable();
            $table->text('average_rating')->nullable();
            $table->text('rating')->nullable();
            $table->text('number_available')->nullable();
            $table->text('custom_1')->nullable();
            $table->text('custom_2')->nullable();
            $table->text('custom_3')->nullable();
            $table->text('custom_4')->nullable();
            $table->text('custom_5')->nullable();
            $table->text('custom_6')->nullable();
            $table->text('custom_7')->nullable();
            $table->text('custom_8')->nullable();
            $table->text('custom_9')->nullable();
            $table->text('ean')->nullable();
            $table->text('isbn')->nullable();
            $table->text('upc')->nullable();
            $table->text('mpn')->nullable();
            $table->text('parent_product_id')->nullable();
            $table->text('product_GTIN')->nullable();
            $table->text('basket_link')->nullable();
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('status')->default('0');
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
