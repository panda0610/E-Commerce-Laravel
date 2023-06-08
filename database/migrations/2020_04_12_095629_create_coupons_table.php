<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'coupons', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code');
            $table->float('discount')->default('0.00');
            $table->integer('limit')->default('0');
            $table->text('description')->nullable();
            $table->integer('is_active')->default('1');
            $table->timestamps();
        }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
