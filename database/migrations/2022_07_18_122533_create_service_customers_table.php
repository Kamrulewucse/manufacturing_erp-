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
        Schema::create('service_customers', function (Blueprint $table) {
            $table->id();
            $table->string('service_customer_code')->nullable();
            $table->string('name');
            $table->string('mobile_no');
            $table->string('alternative_mobile_no')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->nullable();
            $table->string('address');
            $table->tinyInteger('pre_filter')->nullable();
            $table->bigInteger('reference_area_id')->nullable();
            $table->bigInteger('user_id');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('service_customers');
    }
};
