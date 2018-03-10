<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
	/**
     * The table associated with the model
     */
    protected $table = 'barang';

    /**
     * Indicates if the model should be timestamped
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];
}
