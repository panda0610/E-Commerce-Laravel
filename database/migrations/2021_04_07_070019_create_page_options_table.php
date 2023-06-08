<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'page_options', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('enable_page_header')->nullable();
            $table->string('enable_page_footer')->nullable();
            $table->longText('contents')->nullable();
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
        Schema::dropIfExists('page_options');
    }
}
