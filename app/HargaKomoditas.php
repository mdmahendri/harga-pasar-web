<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HargaKomoditas extends Model
{
	/**
     * The table associated with the model
     */
    protected $table = 'harga_komoditas';

    /**
     * Indicates if the model should be timestamped
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
    	'nama_komoditas', 
    	'harga_komoditas',
    	'waktu_catat',
    	'nama_tempat',
    	'latitude',
    	'longitude'
    ];
}
