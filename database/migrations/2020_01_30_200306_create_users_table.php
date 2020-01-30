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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 45)->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('uuid', 255)->unique();
            $table->string('phone', 45)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->string('email', 45)->unique()->nullable();
            $table->string('salt', 255)->nullable();
            $table->string('reset_token', 255)->nullable();
            $table->string('address', 255);
            $table->integer('city_id')->nullable()->unsigned();

            //FK
            $table->foreign('city_id')->references('id')->on('cities');
        });
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
