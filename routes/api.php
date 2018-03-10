<?php

use Illuminate\Http\Request;
use App\HargaBarang;
use App\Barang;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('harga', function(Request $request) {
	$responses = $request->all();
	foreach ($responses as $response) { 
		HargaBarang::create($response);
	};
});

Route::get('barang', function() {
	return Barang::all();
});