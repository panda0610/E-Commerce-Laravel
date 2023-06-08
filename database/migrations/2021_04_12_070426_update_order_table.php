<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'orders', function (Blueprint $table){
            $table->longText('coupon')->nullable()->after('price');
            $table->longText('coupon_json')->nullable()->after('coupon');
            $table->string('discount_price')->nullable()->after('coupon_json');
        }
        );
        Schema::table(
            'stores', function (Blueprint $table){
            $table->string('enable_storelink')->default('on')->after('domains');
            $table->string('enable_subdomain')->nullable()->after('enable_storelink');
            $table->string('subdomain')->nullable()->after('enable_subdomain');
        }
        );
        Schema::table(
            'plans', function (Blueprint $table){
            $table->string('enable_custsubdomain')->default('off')->after('enable_custdomain');
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
            'orders', function (Blueprint $table){
            $table->dropColumn('coupon');
            $table->dropColumn('coupon_json');
            $table->dropColumn('discount_price');
        }
        );
        Schema::table(
            'orders', function (Blueprint $table){
            $table->dropColumn('enable_storelink');
            $table->dropColumn('enable_subdomain');
        }
        );
        Schema::table(
            'plans', function (Blueprint $table){
            $table->dropColumn('enable_custsubdomain');
        }
        );
    }
}
