<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'plans', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name', 100)->unique();
            $table->float('price')->default(0);
            $table->string('duration', 100)->nullable();
            $table->integer('max_stores')->default(0);
            $table->integer('max_products')->default(0);
            $table->string('enable_custdomain')->default('off');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('plans');
    }
}
