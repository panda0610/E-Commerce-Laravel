<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddThemeFieldStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'stores', function (Blueprint $table){
            $table->string('store_theme')->nullable()->after('email');
            $table->string('theme_dir')->nullable()->after('store_theme');
            $table->longText('about')->change();
        }
        );

        Schema::table(
            'products', function (Blueprint $table){
            $table->string('attachment')->nullable()->after('is_cover');
            $table->string('last_price')->nullable()->after('price');
            $table->text('detail')->nullable()->after('description');
            $table->text('specification')->nullable()->after('detail');
        }
        );
        Schema::table(
            'plan_orders', function (Blueprint $table){
            $table->string('coupon')->nullable()->after('price');
            $table->text('coupon_json')->nullable()->after('coupon');
            $table->text('discount_price')->nullable()->after('coupon_json');
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
            'stores', function (Blueprint $table){
            $table->dropColumn('store_theme');
            $table->dropColumn('theme_dir');
            $table->dropColumn('about');
        }
        );
        Schema::table(
            'products', function (Blueprint $table){
            $table->dropColumn('attachment');
            $table->dropColumn('last_price');
            $table->dropColumn('detail');
            $table->dropColumn('specification');
        }
        );
        Schema::table(
            'plan_orders', function (Blueprint $table){
            $table->dropColumn('coupon');
            $table->dropColumn('coupon_json');
            $table->dropColumn('discount_price');
        }
        );
    }
}
