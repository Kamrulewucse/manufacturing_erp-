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
        Schema::create('finished_goods_row_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('finished_goods_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('inventory_id');
            $table->float('per_unit_quantity',50,2);
            $table->float('consumption_quantity',50,2);
            $table->float('consumption_unit_price',50,2);
            $table->float('remain_quantity',50,2);
            $table->float('consumption_loss_quantity_percent',10,2);
            $table->float('consumption_loss_quantity',10,2);
            $table->date('date');
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
        Schema::dropIfExists('finished_goods_row_materials');
    }
};
