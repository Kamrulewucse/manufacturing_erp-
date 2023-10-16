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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('client_code');
            $table->string('name');
            $table->string('mobile_no');
            $table->string('alternative_mobile_no');
            $table->date('date_of_birth');
            $table->string('email');
            $table->string('address')->nullable();
            $table->tinyInteger('pre_filter');
            $table->bigInteger('reference_area_id');
            $table->bigInteger('user_id');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('customers');
    }
};
