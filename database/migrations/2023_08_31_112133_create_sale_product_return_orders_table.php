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
        Schema::create('sale_product_return_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('sale_order_id');
            $table->date('date');
            $table->double('total', 20,2);
            $table->double('paid', 20,2);
            $table->double('due', 20,2);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('sale_product_return_orders');
    }
};
