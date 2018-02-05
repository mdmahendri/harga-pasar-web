<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHargaKomoditasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('harga_komoditas', function (Blueprint $table) {
            $table->increments('id_entry');
            $table->string('nama_komoditas', 100);
            $table->unsignedInteger('harga_komoditas');
            $table->unsignedInteger('waktu_catat');
            $table->string('nama_tempat', 100);
            $table->double('latitude', 8, 5);
            $table->double('longitude', 8, 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('harga_komoditas');
    }
}
