<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('game_defaults', function (Blueprint $table) {
            $table->id();
            $table->time('default_time')->nullable();
            $table->string('default_location')->nullable();
            $table->integer('default_duration')->nullable();
            $table->decimal('default_price', 8, 2)->nullable();
            $table->unsignedBigInteger('default_season_id')->nullable();
            $table->string('default_title_template')->nullable();

            // Optional foreign key to seasons table (if exists)
            if (Schema::hasTable('seasons')) {
                $table->foreign('default_season_id')->references('id')->on('seasons')->onDelete('set null');
            }
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_defaults');
    }
};
