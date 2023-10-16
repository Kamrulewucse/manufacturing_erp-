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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->nullable();
            $table->integer('customer_type')->nullable();
            $table->integer('client_id')->nullable();
            $table->date('date')->nullable();
            $table->double('sub_total')->nullable();
            $table->double('discount')->nullable();
            $table->double('discount_percentage')->nullable();
            $table->double('total')->nullable();
            $table->double('paid')->nullable();
            $table->double('due')->nullable();
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
        Schema::dropIfExists('service_orders');
    }
};
