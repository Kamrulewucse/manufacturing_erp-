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
        Schema::create('sale_price_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_category_id');
            $table->bigInteger('product_sub_category_id');
            $table->tinyInteger('purchase_product_id');
            $table->double('customer_selling_price',20,2);
            $table->double('dealer_selling_price',20,2);
            $table->double('distributor_selling_price',20,2);
            $table->boolean('status');
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
        Schema::dropIfExists('sale_price_settings');
    }
};
