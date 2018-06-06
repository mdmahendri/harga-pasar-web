<?php

use Illuminate\Http\Request;
use App\HargaKonsumen;
use App\Barang;
use App\Account;

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
	$plus = count($responses);

	DB::beginTransaction();
	try {
		//
		foreach ($responses as $response) { 
			HargaKonsumen::create($response);
		};

		// update the points
		$mail = $responses[0]['mail'];
		$account = Account::find($mail);
		if ($account) {
			$account->points = $account->points + $plus;
			$account->save();
		} else {
			$account = new Account;
			$account->mail = $mail;
			$account->points = $plus;
			$account->save();
		}

	} catch (Excepton $e) {
		DB::rollback();
		return $e;
	}

	DB::commit();
	return response('success', 200);
});

Route::get('points/{mail}', function($mail){
	$account = Account::where('mail', $mail)->first();
	return response($account? $account->points : 0, 200);
});

Route::get('barang', function() {
	return Barang::all();
});

Route::apiResource('pasar', 'API\PasarController');