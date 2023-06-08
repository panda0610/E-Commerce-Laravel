<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwilioToStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('is_twilio_enabled',225)->nullable()->after('invoice_logo');
            $table->text('twilio_sid')->nullable()->after('is_twilio_enabled');
            $table->text('twilio_token')->nullable()->after('twilio_sid');
            $table->text('twilio_from')->nullable()->after('twilio_token');
            $table->text('notification_number')->nullable()->after('twilio_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
             $table->dropColumn('is_twilio_enabled');
             $table->dropColumn('twilio_sid');
             $table->dropColumn('twilio_token');
             $table->dropColumn('twilio_from');
             $table->dropColumn('notification_number');
        });
    }
}
