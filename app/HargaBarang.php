<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HargaBarang extends Model
{
	/**
     * The table associated with the model
     */
    protected $table = 'harga_barang';

    /**
     * Indicates if the model should be timestamped
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
    	'nama_barang', 
    	'harga_barang',
    	'waktu_catat',
    	'nama_tempat',
    	'latitude',
    	'longitude'
    ];
}
