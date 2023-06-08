<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'product_coupons', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('enable_flat')->default('off')->nullable();
            $table->float('discount')->default('0.00');
            $table->float('flat_discount')->default('0.00')->nullable();
            $table->integer('limit')->default('0');
            $table->text('description')->nullable();
            $table->integer('store_id')->default(0);
            $table->integer('created_by')->default(0);
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
        Schema::dropIfExists('product_coupons');
    }
}
