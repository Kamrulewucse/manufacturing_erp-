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
        Schema::create('salary_change_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->nullable();
            $table->text('date')->nullable();
            $table->integer('type')->nullable();
            $table->double('basic_salary', 20, 2)->default(0);
            $table->double('others_deduct', 20, 2)->default(0);
            $table->double('tax', 20, 2)->default(0);
            $table->double('house_rent', 20, 2)->default(0);
            $table->double('travel', 20, 2)->default(0);
            $table->double('medical', 20, 2)->default(0);
            $table->double('gross_salary', 20, 2)->default(0);
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
        Schema::dropIfExists('salary_change_logs');
    }
};
