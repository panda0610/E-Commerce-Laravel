<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'stores', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('domains')->nullable();
            $table->string('enable_domain')->default('off');
            $table->string('about')->nullable();
            $table->string('tagline')->nullable();
            $table->string('slug')->nullable();
            $table->string('lang', 5)->default('en');
            $table->string('storejs')->nullable();
            $table->string('currency')->default('$');
            $table->string('currency_code')->default('USD');
            $table->string('currency_symbol_position')->default('pre')->nullable();
            $table->string('currency_symbol_space')->default('without')->nullable();
            $table->string('whatsapp')->default('#');
            $table->string('facebook')->default('#');
            $table->string('instagram')->default('#');
            $table->string('twitter')->default('#');
            $table->string('youtube')->default('#');
            $table->string('google_analytic')->nullable();
            $table->string('footer_note')->nullable();
            $table->string('enable_header_img')->default('on');
            $table->string('header_img')->nullable();
            $table->string('header_title')->nullable();
            $table->string('header_desc')->nullable();
            $table->string('button_text')->nullable();
            $table->string('enable_subscriber')->default('on');
            $table->string('enable_rating')->default('on');
            $table->string('sub_img')->nullable();
            $table->string('subscriber_title')->nullable();
            $table->string('sub_title')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('country')->nullable();
            $table->string('logo')->nullable();
            $table->string('is_stripe_enabled')->default('off');
            $table->text('stripe_key')->nullable();
            $table->text('stripe_secret')->nullable();
            $table->string('is_paypal_enabled')->default('off');
            $table->text('paypal_mode')->nullable();
            $table->text('paypal_client_id')->nullable();
            $table->text('paypal_secret_key')->nullable();
            $table->text('mail_driver')->nullable();
            $table->text('mail_host')->nullable();
            $table->text('mail_port')->nullable();
            $table->text('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->text('mail_encryption')->nullable();
            $table->text('mail_from_address')->nullable();
            $table->text('mail_from_name')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('created_by')->default(0);
            $table->string('enable_whatsapp')->default('off');
            $table->string('whatsapp_number')->nullable();
            $table->string('enable_cod')->default('off');
            $table->string('enable_bank')->default('off');
            $table->string('bank_number')->nullable();
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
        Schema::dropIfExists('stores');
    }
}
