<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLocationField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'user_details', function (Blueprint $table){
            $table->integer('location_id')->default(0)->after('shipping_postalcode');
            $table->integer('shipping_id')->default(0)->after('location_id');
        }
        );
        Schema::table(
            'stores', function (Blueprint $table){
            $table->string('enable_shipping')->default('on')->after('enable_rating');
        }
        );
        Schema::table(
            'orders', function (Blueprint $table){
            $table->longText('shipping_data')->nullable()->after('product_id');
        }
        );
        Schema::table(
            'products', function (Blueprint $table){
            $table->string('enable_product_variant')->default('off')->after('product_display');
            $table->longText('variants_json')->nullable()->after('enable_product_variant');
        }
        );
        Schema::table(
            'shippings', function (Blueprint $table){
            $table->string('price')->after('name');
            $table->string('location_id')->default(0)->after('price');
            $table->integer('store_id')->default(0)->after('location_id');
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
        Schema::table(
            'user_details', function (Blueprint $table){
            $table->dropColumn('location_id');
            $table->dropColumn('shipping_id');
        }
        );
        Schema::table(
            'stores', function (Blueprint $table){
            $table->dropColumn('enable_shipping');
        }
        );
        Schema::table(
            'orders', function (Blueprint $table){
            $table->dropColumn('shipping_data');
        }
        );
        Schema::table(
            'products', function (Blueprint $table){
            $table->dropColumn('enable_product_variant');
            $table->dropColumn('variants_json');
        }
        );
        Schema::table(
            'shippings', function (Blueprint $table){
            $table->dropColumn('price');
            $table->dropColumn('location_id');
            $table->dropColumn('store_id');
        }
        );
    }
}
