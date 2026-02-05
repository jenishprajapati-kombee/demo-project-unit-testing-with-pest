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
        Schema::create('brands', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment('AUTO_INCREMENT');
            $table->string('name', 191)->nullable();
            $table->text('remark')->nullable();
            $table->dateTime('bob')->nullable();
            $table->string('description', 500)->nullable();

            $table->unsignedInteger('country_id')->nullable()->comment('Countries table ID');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->unsignedInteger('state_id')->nullable()->comment('States table ID');
            $table->foreign('state_id')->references('id')->on('states');

            $table->unsignedInteger('city_id')->nullable()->comment('Cities table ID');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->char('status', 1)->nullable()->comment('Y => Active, N => Inactive');
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
        Schema::dropIfExists('brands');
    }
};
