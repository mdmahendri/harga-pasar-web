<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * Indicates if the model should be timestamped
     */
    public $timestamps = false;

    /**
     * Define custom primary key field
     */
    protected $primaryKey = 'mail';
}
