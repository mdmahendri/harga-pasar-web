<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHargaKonsumenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('harga_konsumen', function (Blueprint $table) {
            $table->increments('id_entry');
            $table->integer('id_barang');
            $table->unsignedInteger('harga');
            $table->unsignedBigInteger('waktu_catat');
            $table->string('nama_tempat', 100);
            $table->double('latitude', 8, 5);
            $table->double('longitude', 8, 5);
            $table->string('mail', 30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('harga_konsumen');
    }
}
