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
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_category_id');
            $table->bigInteger('product_sub_category_id');
            $table->tinyInteger('product_type');
            $table->string('name',255);
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('purchase_products');
    }
};
