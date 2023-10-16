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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type');
            $table->unsignedInteger('product_id');
            $table->string('serial')->nullable();
            $table->double('quantity',8,2);
            $table->double('unit_price',20,2)->default(0.00);
            $table->double('selling_price',20,2)->default(0.00);
            $table->double('total',20,2)->default(0.00);
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
        Schema::dropIfExists('inventories');
    }
};
