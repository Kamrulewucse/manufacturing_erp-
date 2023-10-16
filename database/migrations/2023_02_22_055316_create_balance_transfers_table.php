<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sister_concern_id');
            $table->string('financial_year');
            $table->date('date');
            $table->string('voucher_no');
            $table->string('receipt_no');
            $table->integer('type')->comment('1=Bank To Cash; 2=Cash To Bank; 3=Bank To Bank,4=Cash to Cash');
            $table->unsignedBigInteger('source_account_head_id');
            $table->unsignedBigInteger('target_account_head_id');
            $table->date('source_cheque_date')->nullable();
            $table->string('source_cheque_no')->nullable();
            $table->date('target_cheque_date')->nullable();
            $table->string('target_cheque_no')->nullable();
            $table->float('amount',100,2);
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
        Schema::dropIfExists('balance_transfers');
    }
};
