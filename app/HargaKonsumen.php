<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HargaKonsumen extends Model
{
	/**
     * The table associated with the model
     */
    protected $table = 'harga_konsumen';

    /**
     * Indicates if the model should be timestamped
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
    	'id_barang', 
    	'harga',
    	'waktu_catat',
    	'nama_tempat',
    	'latitude',
    	'longitude'
    ];
}
