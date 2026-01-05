<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('season_number')->default(1);
            $table->timestamps();
            // Add other columns as needed
        });
    }

    public function down()
    {
        Schema::dropIfExists('seasons');
    }
};
