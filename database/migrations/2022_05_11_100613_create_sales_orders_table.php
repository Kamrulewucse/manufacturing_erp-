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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->unsignedInteger('warehouse_id');
            $table->unsignedInteger('service_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('dealer_id')->nullable();
            $table->unsignedInteger('zone_id');
            $table->unsignedInteger('employee_id');
            $table->tinyInteger('customer_type');
            $table->date('install_date');
            $table->time('install_time');
            $table->float('sub_total', 20);
            $table->float('vat_percentage', 20);
            $table->float('vat', 20);
            $table->float('discount_percentage', 20);
            $table->float('discount', 20);
            $table->float('total', 20);
            $table->float('paid', 20);
            $table->float('due', 20);
            $table->float('new_setup_sub_total', 20)->default(0.00);
            $table->float('new_setup_due', 20)->default(0.00);
            $table->unsignedInteger('user_id');
            $table->tinyInteger('approve_status');
            $table->tinyInteger('pre_filter')->nullable();
            $table->string('sale_note')->nullable();
            $table->string('service_note')->nullable();
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
        Schema::dropIfExists('sales_orders');
    }
};
