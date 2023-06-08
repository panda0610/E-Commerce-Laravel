<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'user_details', function (Blueprint $table){
            $table->id();
            $table->string('store_id');
            $table->string('name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email');
            $table->string('billing_address');
            $table->string('billing_country');
            $table->string('billing_city');
            $table->integer('billing_postalcode');
            $table->string('shipping_address');
            $table->string('shipping_country');
            $table->string('shipping_city');
            $table->integer('shipping_postalcode');
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
        Schema::dropIfExists('user_details');
    }
}
