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
        Schema::create('technician_conveyance_requisition_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('technician_conveyance_requisition_id');
            $table->bigInteger('service_id');
            $table->text('conveyance_from');
            $table->text('conveyance_to');
            $table->double('amount',20,2)->default('0');
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
        Schema::dropIfExists('technician_conveyance_requisition_details');
    }
};
