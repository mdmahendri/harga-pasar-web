<?php

namespace App\Http\Controllers\API;

use App\Pasar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PasarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $responses = $request->all();
        foreach ($responses as $row) Pasar::updateVersion($row);
        return response('success', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $latLng in format {lat,lng}
     * @return \Illuminate\Http\Response
     */
    public function show($latLng)
    {
        // get pasar near user
        $location = explode(',', $latLng);
        $pasarCollection = Pasar::near($location[0], $location[1]);

        // make API call to Google Map if result < 5
        if ($pasarCollection->count() < 5) {

            $client = new \GuzzleHttp\Client([
                'base_uri' => 'https://maps.googleapis.com/maps/api/'
            ]);

            $response = $client->request('GET', 'place/nearbysearch/json',
                ['query' => [
                    'keyword' => 'pasar traditional market',
                    'rankby' => 'distance',
                    'language' => 'id',
                    'key' => 'AIzaSyAEgtC9V3nkObNZSHZpfVx48cd74ckX9ao',
                    'location' => $latLng
                ], 'debug' => false]
            );

            Pasar::fromGoogleMap($response);


            // query once more
            $pasarCollection = Pasar::near($location[0], $location[1]);
        }

        return response($pasarCollection->toJson(), 200)
            ->header('Content-Type', 'application/json');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
