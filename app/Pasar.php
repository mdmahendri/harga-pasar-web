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
     * Indicates if the IDs are auto-incrementing
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'id',
        'nama',
        'alamat',
        'latitude',
        'longitude',
        'version'
    ];

    /**
     * Save response from Map API
     */
    public static function fromGoogleMap($response) {
        $pasarArray = json_decode($response->getBody())->results;
    	foreach ($pasarArray as $pasar) {
            $row = array(
                'id' => $pasar->id,
                'nama' => $pasar->name,
                'alamat' => $pasar->vicinity,
                'latitude' => $pasar->geometry->location->lat,
                'longitude' => $pasar->geometry->location->lng
            );
            self::updateVersion($row);
    	}
    }

    public static function near($lat, $lng) {
        return self::selectRaw(
            'id, nama, alamat, latitude, longitude, version, (6371 * acos (cos ( radians(?) ) * cos(radians(latitude) ) * cos(radians(longitude) - radians(?) ) + sin ( radians(?) ) * sin( radians(latitude) ))) AS distance', [$lat, $lng, $lat])
        ->having('distance', '<', 30)
        ->get();
    }

    public static function updateVersion($row) {

        if (self::where('id', $row['id'])->exists()) {
            self::where('id', $row['id'])
                ->where('version', $row['version'])
                ->update([
                    'nama' => $row['nama'],
                    'alamat' => $row['alamat'],
                    'latitude' => $row['latitude'],
                    'longitude' => $row['longitude'],
                    'version' => ($row['version'] + 1)
                ]);

        } else {
            // from android, create new version 0
            $row['version'] = 1;
            self::create($row);
        }

    }

}
