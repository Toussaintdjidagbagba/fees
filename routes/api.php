<?php

use Illuminate\Http\Request;

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

Route::resource('apicommission', 'API\APICommissionG');
Route::resource('apicommerciaux', 'API\APICommerciauxG');
Route::resource('reclamation', 'API\APIReclamation');
Route::resource('impautocontrat', 'API\APIImportationContrat');
Route::post('impauto', 'API\APIImportationContrat@uploadfilecontrat');
Route::post('impautocom', 'API\APIImportationCommission@uploadfilecommission');
Route::resource('impautocomgroup', 'API\APIImportationCommissionGroupe');
Route::resource('commerciaux', 'API\APIHieararchie');//->middleware("throttle:300:1");
Route::resource('quittances', 'API\APIQuittance');