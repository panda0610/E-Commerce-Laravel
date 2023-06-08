<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewFieldTable extends Migration
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
            $table->string('invoice_logo')->nullable()->after('logo');
            $table->string('blog_enable')->default('on')->after('enable_rating');
        }
        );
        Schema::table(
            'plans', function (Blueprint $table){
            $table->string('additional_page')->nullable()->after('enable_custdomain');
            $table->string('blog')->nullable()->after('additional_page');
            $table->string('shipping_method')->nullable()->after('blog');
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
            $table->dropColumn('invoice_logo');
            $table->dropColumn('blog_enable');
        }
        );
        Schema::table(
            'plans', function (Blueprint $table){
            $table->dropColumn('additional_page');
            $table->dropColumn('blog');
            $table->dropColumn('shipping_method');
        }
        );
    }
}
