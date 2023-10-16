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
        Schema::create('service_order_details', function (Blueprint $table) {
            $table->id();
            $table->integer('service_order_id');
            $table->integer('client_id');
            $table->integer('product_type');
            $table->integer('category_id');
            $table->integer('product_id');
            $table->string('serial')->nullable();
            $table->date('date')->nullable();
            $table->double('quantity')->nullable();
            $table->double('unit_price')->nullable();
            $table->double('selling_price')->nullable();
            $table->double('total')->nullable();
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
        Schema::dropIfExists('service_order_details');
    }
};
