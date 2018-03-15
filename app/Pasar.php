<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pasar extends Model
{
	/**
     * The table associated with the model
     */
    protected $table = 'pasar';

    /**
     * The storage format of the model's date columns.
     * use unix epoch
     */
    protected $dateFormat = 'U';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'map_id',
        'nama',
        'alamat',
        'latitude',
        'longitude'
    ];

    /**
     * Save response from Map API
     */
    public static function fromGoogleMap($response) {
        $pasarArray = json_decode($response->getBody())->results;
    	foreach ($pasarArray as $pasar) {
    		$goblok = static::firstOrCreate(
    			['map_id' => $pasar->id],
    			[
                    'nama' => $pasar->name,
                    'alamat' => $pasar->vicinity,
                    'latitude' => $pasar->geometry->location->lat,
                    'longitude' => $pasar->geometry->location->lng
                ]
    		);
    	}
    }

    public static function near($lat, $lng) {
        return self::selectRaw(
            'id, nama, alamat, latitude, longitude, (6371 * acos (cos ( radians(?) ) * cos(radians(latitude) ) * cos(radians(longitude) - radians(?) ) + sin ( radians(?) ) * sin( radians(latitude) ))) AS distance', [$lat, $lng, $lat])
        ->having('distance', '<', 30)
        ->get();
    }

}
