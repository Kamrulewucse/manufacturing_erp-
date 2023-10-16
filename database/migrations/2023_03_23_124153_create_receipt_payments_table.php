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
        Schema::create('receipt_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('payee_depositor_account_head_id')->nullable();
            $table->unsignedBigInteger('tax_section_id')->nullable();
            $table->unsignedBigInteger('payment_account_head_id');
            $table->string('financial_year');
            $table->date('date');
            $table->string('receipt_payment_no');
            $table->integer('transaction_type')->comment('1=receipt,2=payment');
            $table->integer('payment_type')->comment('1=bank,2=cash');
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('issuing_bank_name')->nullable();
            $table->string('issuing_branch_name')->nullable();
            $table->string('issuing_account_name')->nullable();
            $table->string('issuing_account_no')->nullable();
            $table->string('e_tin')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('is_delete')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('receipt_payments');
    }
};
