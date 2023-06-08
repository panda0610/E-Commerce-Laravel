<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('lang')->nullable();
            $table->integer('current_store')->nullable();
            $table->string('avatar')->nullable();
            $table->string('type', 20)->default('user');
            $table->integer('plan')->default(1);
            $table->date('plan_expire_date')->nullable();
            $table->integer('created_by')->default(0);
            $table->string('mode')->default('light');
            $table->integer('plan_is_active')->default(1);
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
        Schema::dropIfExists('users');
    }
}
