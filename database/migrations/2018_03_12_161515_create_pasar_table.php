<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pasar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('map_id', 50);
            $table->string('nama', 100);
            $table->string('alamat', 150);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('created_at');
            $table->integer('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pasar');
    }
}
