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
        Schema::create('user_tags', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment('AUTO_INCREMENT');

            $table->unsignedInteger('user_id')->index()->nullable()->comment('Users table ID');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('keyword', 191);
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
        Schema::dropIfExists('user_tags');
    }
};
