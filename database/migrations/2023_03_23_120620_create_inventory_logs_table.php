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
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->nullable();
            $table->tinyInteger('product_type');
            $table->string('serial')->nullable();
            $table->integer('type')->comment('1=In; 2=Out');
            $table->date('date');
            $table->double('quantity',8,2);
            $table->double('unit_price',20,2)->default(0.00);
            $table->double('selling_price',20,2)->default(0.00);
            $table->double('sale_total',20,2)->default(0.00);
            $table->double('total',20,2)->default(0.00);
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('sales_order_id')->nullable();
            $table->unsignedInteger('purchase_order_id')->nullable();
            $table->unsignedInteger('purchase_inventory_id')->nullable();
            $table->string('note')->nullable();
            $table->unsignedInteger('sales_order_no')->nullable();
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
        Schema::dropIfExists('inventory_logs');
    }
};
