<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddStoreCustomFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'products', function (Blueprint $table){
            $table->string('custom_field_1')->nullable()->after('product_tax');
            $table->string('custom_value_1')->nullable()->after('custom_field_1');
            $table->string('custom_field_2')->nullable()->after('custom_value_1');
            $table->string('custom_value_2')->nullable()->after('custom_field_2');
            $table->string('custom_field_3')->nullable()->after('custom_value_2');
            $table->string('custom_value_3')->nullable()->after('custom_field_3');
            $table->string('custom_field_4')->nullable()->after('custom_value_3');
            $table->string('custom_value_4')->nullable()->after('custom_field_4');
        }
        );
        Schema::table(
            'user_details', function (Blueprint $table){
            $table->string('custom_field_title_1')->nullable()->after('shipping_address');
            $table->string('custom_field_title_2')->nullable()->after('custom_field_title_1');
            $table->string('custom_field_title_3')->nullable()->after('custom_field_title_2');
            $table->string('custom_field_title_4')->nullable()->after('custom_field_title_3');
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
        Schema::dropIfExists('add_store_custom_field');
    }
}
