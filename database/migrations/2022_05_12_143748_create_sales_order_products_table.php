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
        Schema::create('sales_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sales_order_id');
            $table->unsignedInteger('product_category_id');
            $table->unsignedInteger('product_sub_category_id');
            $table->unsignedInteger('purchase_product_id');
            $table->unsignedInteger('zone_id');
            $table->unsignedInteger('employee_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('dealer_id');
            $table->unsignedInteger('lc_id');
            $table->unsignedInteger('warehouse_id');
            $table->string('product_customer_code');
            $table->time('install_time');
            $table->date('install_date');
            $table->date('last_service_date');
            $table->date('next_service_date');
            $table->date('end_service_date');
            $table->tinyInteger('sp_non_sp_status');
            $table->tinyInteger('new_setup_status');
            $table->tinyInteger('sale_approved_status');
            $table->tinyInteger('exchange_status');
            $table->tinyInteger('total_exchange');
            $table->tinyInteger('total_service');
            $table->tinyInteger('remaining_sp_service');
            $table->float('quantity');
            $table->float('unit_price', 20);
            $table->float('total', 20);
            $table->tinyInteger('accessories_payment_type')->nullable()->comment('1=cash;2=online');
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
        Schema::dropIfExists('sales_order_products');
    }
};
