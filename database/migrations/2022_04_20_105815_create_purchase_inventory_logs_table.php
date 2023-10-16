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
        Schema::create('purchase_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_product_id');
            $table->unsignedInteger('product_category_id');
            $table->unsignedInteger('product_sub_category_id');
            $table->tinyInteger('product_type');
            $table->tinyInteger('type')->comment('1=In; 2=Out');
            $table->date('date');
            $table->unsignedInteger('warehouse_id');
            $table->float('quantity', 20);
            $table->float('unit_price', 20);
            $table->float('selling_price', 20);
            $table->float('total', 20);
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('purchase_order_id')->nullable();
            $table->unsignedInteger('purchase_inventory_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('sales_order_id')->nullable();
            $table->string('note')->nullable();
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('purchase_inventory_logs');
    }
};
