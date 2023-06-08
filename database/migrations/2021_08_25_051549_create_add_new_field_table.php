<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddNewFieldTable extends Migration
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
            $table->string('enable_telegram')->nullable()->after('whatsapp_number');
            $table->string('telegrambot')->nullable()->after('enable_telegram');
            $table->string('telegramchatid')->nullable()->after('telegrambot');
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
        Schema::dropIfExists('telegrambot');
        Schema::dropIfExists('telegramchatid');
    }
}
