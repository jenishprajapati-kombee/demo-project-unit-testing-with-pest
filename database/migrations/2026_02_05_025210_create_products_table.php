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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment('AUTO_INCREMENT');
            $table->string('name', 191)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('code', 20);
            $table->decimal('price', 10, 2)->nullable();
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
        Schema::dropIfExists('products');
    }
};
