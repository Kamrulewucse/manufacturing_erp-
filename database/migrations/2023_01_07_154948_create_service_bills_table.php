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
        Schema::create('service_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('service_id');
            $table->unsignedInteger('employee_id');
            $table->double('sub_total');
            $table->float('service_charge',2);
            $table->float('discount',2);
            $table->double('total',2);
            $table->tinyInteger('status')->comment('1=Bill not Complete,2=Bill Complete');
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
        Schema::dropIfExists('service_bills');
    }
};
