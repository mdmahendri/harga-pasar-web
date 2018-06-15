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

Route::get('revgeo', function() {
	set_time_limit(0);
	header('Content-Type: application/octet-stream');
	header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
	// Turn off output buffering
	ini_set('output_buffering', 'off');
	// Turn off PHP output compression
	ini_set('zlib.output_compression', false);
	// Implicitly flush the buffer(s)
	ini_set('implicit_flush', true);
	ob_implicit_flush(true);
	// Clear, and turn off output buffering
	while (ob_get_level() > 0) {
		// Get the curent level
		$level = ob_get_level();
		// End the buffering
		ob_end_clean();
		// If the current level has not changed, abort
		if (ob_get_level() == $level) break;
	}

	$counter = 0;

	$list_null = HargaKonsumen::select('nama_tempat')
		->whereNull('provinsi')
		->groupBy('nama_tempat')
		->get();

	foreach ($list_null as $value) {
		$harga = HargaKonsumen::where('nama_tempat', $value->nama_tempat)
			->orderByRaw('-provinsi', 'DESC')
			->first();

		$prov_name = '';
		if ($harga->provinsi) {
			$prov_name = $harga->provinsi;
		} else {
			$prov_name = Geocoder::reverse($harga->latitude, $harga->longitude)
				->get()->first()
				->getAdminLevels()->first()
				->getName();
		}

		$updated = HargaKonsumen::where('nama_tempat', $harga->nama_tempat)
			->whereNull('provinsi')
			->update(['provinsi' => $prov_name]);
		
		$counter += $updated;
		echo 'n' . $counter;
	}
});

Route::get('data/{loc}/{start}/{end}', function($loc, $start, $end) {
	$status = '';
	$loc = ucwords(str_replace('-', ' ', $loc));

	$results = DB::table('harga_konsumen')
		->where('provinsi', $loc)
		->whereBetween('waktu_catat', [$start, $end])
		->join('barang', 'barang.id_barang', '=', 'harga_konsumen.id_barang')
		->selectRaw('barang.nama, barang.kualitas, avg(harga) as harga')
		->groupBy('barang.nama', 'barang.kualitas')
		->get();

	if ($results->isEmpty()) {
		$last_data = HargaKonsumen::where('provinsi', $loc)
			->orderBy('waktu_catat', 'desc')
			->first();
		if($last_data) {
			$last_data = date('d M Y', $last_data->waktu_catat / 1000);
			$results = "Data input terakhir pada $last_data";
		} else {
			$results = "Belum terdapat data pada Provinsi $loc";
		}

		$status = 'fail';
	} else {
		$status = 'success';
	}

	return response()->json([
		'status' => $status,
		'data' => $results
	]);
});

Route::apiResource('pasar', 'API\PasarController');