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
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('financial_year');
            $table->date('date');
            $table->string('jv_no');
            $table->unsignedBigInteger('payee_depositor_account_head_id')->nullable();
            $table->unsignedBigInteger('tax_section_id')->nullable();
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
        Schema::dropIfExists('journal_vouchers');
    }
};
