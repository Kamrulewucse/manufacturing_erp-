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
        Schema::create('config_product_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('config_product_id');
            $table->unsignedBigInteger('product_id');
            $table->float('quantity');
            $table->float('loss_quantity_percent')->default(0);
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
        Schema::dropIfExists('config_product_details');
    }
};
