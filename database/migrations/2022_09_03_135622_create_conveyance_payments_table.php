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
        Schema::create('conveyance_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('technician_conveyance_requisition_id');
            $table->unsignedInteger('employee_id');
            $table->tinyInteger('transaction_method')->comment('1=Cash; 2=Bank; 3=Mobile Banking');
            $table->unsignedInteger('mobile_bank_id')->nullable();
            $table->unsignedInteger('bank_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('bank_account_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('cheque_image')->nullable();
            $table->float('amount', 20);
            $table->date('date');
            $table->string('note')->nullable();
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
        Schema::dropIfExists('conveyance_payments');
    }
};
