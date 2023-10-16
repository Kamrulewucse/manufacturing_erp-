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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->unsignedInteger('supplier_id');
            $table->unsignedInteger('warehouse_id');
            $table->date('date');
            $table->tinyInteger('product_type')->comment('1=Model/Machine Product 2=Accessory Product');
            $table->double('transport_cost', 20,2);
            $table->double('total', 20,2);
            $table->double('paid', 20,2);
            $table->double('due', 20,2);
            $table->text('note')->nullable();
            $table->date('next_payment');
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
        Schema::dropIfExists('purchase_orders');
    }
};
