<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('bank_id');
            $table->unsignedInteger('branch_id');
            $table->string('account_name');
            $table->string('account_no');
            $table->string('existing_account_code')->nullable();
            $table->string('general_ledger');
            $table->string('sub_ledger');
            $table->string('sub_subsidiary');
            $table->string('account_code');
            $table->integer('type');
            $table->float('opening_balance', 100)->default(0);
            $table->float('balance', 100)->default(0);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('bank_accounts');
    }
}
