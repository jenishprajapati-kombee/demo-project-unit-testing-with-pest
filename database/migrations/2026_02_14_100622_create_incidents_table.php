<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->json('raw_logs');
            $blueprint->json('raw_metrics');
            $blueprint->string('severity')->index();
            $blueprint->string('likely_cause')->nullable();
            $blueprint->float('confidence')->default(0);
            $blueprint->text('reasoning')->nullable();
            $blueprint->text('next_steps')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
