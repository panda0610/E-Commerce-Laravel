<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteStoreFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumn('store', 'facebook'))
        {
            Schema::table(
                'store', function (Blueprint $table){
                $table->dropColumn('whatsapp');
                $table->dropColumn('facebook');
                $table->dropColumn('instagram');
                $table->dropColumn('twitter');
                $table->dropColumn('youtube');
                $table->dropColumn('footer_note');
                $table->dropColumn('storejs');
                $table->dropColumn('enable_header_img');
                $table->dropColumn('header_img');
                $table->dropColumn('header_title');
                $table->dropColumn('header_desc');
                $table->dropColumn('button_text');
                $table->dropColumn('enable_subscriber');
                $table->dropColumn('sub_img');
                $table->dropColumn('subscriber_title');
                $table->dropColumn('sub_title');
            }
            );
        }

        Schema::table('product_categories', function (Blueprint $table){
            $table->string('categorie_img')->nullable()->after('name');

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
        //
    }
}
