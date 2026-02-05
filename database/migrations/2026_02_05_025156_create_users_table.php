<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment('AUTO_INCREMENT');
            $table->string('name', 191)->nullable();
            $table->string('email', 191)->unique()->index()->nullable();
            $table->string('password', 191)->nullable();

            $table->unsignedInteger('role_id')->index()->nullable()->comment('Roles table ID');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->date('dob')->nullable();
            $table->string('profile', 191)->nullable();

            $table->unsignedInteger('country_id')->nullable()->comment('Countries table ID');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->unsignedInteger('state_id')->nullable()->comment('States table ID');
            $table->foreign('state_id')->references('id')->on('states');

            $table->unsignedInteger('city_id')->nullable()->comment('Cities table ID');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->char('gender', 1)->nullable()->comment('F => Female, M => Male');
            $table->char('status', 1)->index()->nullable()->comment('Y => Active, N => Inactive');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 191)->nullable();
            $table->char('locale', 6)->index()->nullable()->default('en');
            $table->unsignedInteger('created_by')->nullable()->comment('');
            $table->unsignedInteger('updated_by')->nullable()->comment('');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
