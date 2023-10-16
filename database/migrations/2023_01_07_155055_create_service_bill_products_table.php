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
        Schema::create('service_bill_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('service_bill_id');
            $table->unsignedInteger('product_category_id');
            $table->unsignedInteger('product_sub_category_id');
            $table->unsignedInteger('purchase_product_id');
            $table->integer('quantity');
            $table->double('unit_price',2);
            $table->double('total',2);
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
        Schema::dropIfExists('service_bill_products');
    }
};
