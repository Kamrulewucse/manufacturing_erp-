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
        Schema::create('receipt_payment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receipt_payment_id');
            $table->unsignedBigInteger('account_head_id');
            $table->unsignedBigInteger('parent_deduction_account_head_id')->nullable();
            $table->unsignedBigInteger('vat_account_head_id')->nullable();
            $table->unsignedBigInteger('ait_account_head_id')->nullable();
            $table->boolean('other_head')->default(0);
            $table->float('vat_base_amount',100,2)->default(0);
            $table->float('vat_rate',10,2)->default(0);
            $table->float('vat_amount',100,2)->default(0);
            $table->float('ait_base_amount',100,2)->default(0);
            $table->float('ait_rate',10,2)->default(0);
            $table->float('ait_amount',100,2)->default(0);
            $table->float('other_amount',100,2)->default(0);
            $table->float('amount',100,2)->default(0);
            $table->float('net_total',100,2)->default(0);
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
        Schema::dropIfExists('receipt_payment_details');
    }
};
