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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('particular');
            $table->tinyInteger('transaction_type')->comment('1=Income; 2=Expense');
            $table->tinyInteger('transaction_method')->comment('1=Cash; 2=Bank');
            $table->unsignedInteger('bank_id')->nullable();
            $table->unsignedInteger('branch_id')->nullable();
            $table->unsignedInteger('bank_account_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('cheque_image')->nullable();
            $table->unsignedInteger('mobile_bank_id')->nullable();
            $table->float('amount', 20);
            $table->string('note')->nullable();
            $table->unsignedInteger('purchase_payment_id')->nullable();
            $table->unsignedInteger('supplier_id')->nullable();
            $table->unsignedInteger('sale_payment_id')->nullable();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
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
        Schema::dropIfExists('transaction_logs');
    }
};
