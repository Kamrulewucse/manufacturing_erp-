<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_order_id');
            $table->unsignedInteger('product_category_id');
            $table->unsignedInteger('product_sub_category_id');
            $table->unsignedInteger('purchase_product_id');
            $table->unsignedInteger('product_type');
            $table->string('name');
            $table->string('lc_no');
            $table->float('quantity');
            $table->float('unit_price', 20);
            $table->float('selling_price', 20);
            $table->float('total', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order_products');
    }
};
