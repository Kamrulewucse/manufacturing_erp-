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
        Schema::create('account_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('existing_account_code')->nullable();
            $table->string('general_ledger');
            $table->string('sub_ledger');
            $table->string('sub_subsidiary');
            $table->string('account_code');
            $table->integer('type');
            $table->integer('sub_type')->nullable();
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
        Schema::dropIfExists('account_heads');
    }
};
