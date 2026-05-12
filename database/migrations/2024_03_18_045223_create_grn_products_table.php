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
        Schema::create('grn_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('grn_id');
            $table->foreign('grn_id')->references('id')->on('grns');
            $table->unsignedInteger('purchase_order_product_id');
            $table->foreign('purchase_order_product_id')->references('id')->on('purchase_order_products');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer('received_quantity');
            $table->decimal('received_price', 10, 2);
            $table->integer('out_quantity')->nullable(); // Making out_quantity nullable
            $table->integer('issued_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_products');
    }
};
