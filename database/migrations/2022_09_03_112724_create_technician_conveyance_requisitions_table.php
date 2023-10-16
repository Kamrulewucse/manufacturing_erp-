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
        Schema::create('technician_conveyance_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_no',255);
            $table->bigInteger('requisition_id');
            $table->text('requisition_note')->nullable();
            $table->double('total',20,2)->default('0');
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
        Schema::dropIfExists('technician_conveyance_requisitions');
    }
};
