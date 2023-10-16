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
        Schema::create('purchase_inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_product_id');
            $table->unsignedInteger('product_category_id');
            $table->unsignedInteger('product_sub_category_id');
            $table->tinyInteger('product_type');
            $table->unsignedInteger('warehouse_id');
            $table->double('quantity', 20,2);
            $table->double('unit_price',20,2);
            $table->double('selling_price',20,2);
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
        Schema::dropIfExists('purchase_inventories');
    }
};
