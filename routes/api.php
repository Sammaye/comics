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

Route::prefix(config('app.base_url'))->group(function () {

    Route::prefix('comic')->name('comic.')->group(function () {
        Route::get('get-names', 'ApiComicController@getNames')
            ->name('getNames');

        Route::get('get/{comicId?}/{index?}', 'ApiComicController@get')
            ->name('get');

        Route::post('{comic}/subscribe', 'ApiComicController@subscribe')
            ->name('subscribe')
            ->middleware('auth:api');
        Route::post('{comic}/unsubscribe', 'ApiComicController@unsubscribe')
            ->name('unsubscribe')
            ->middleware('auth:api');
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('user', 'ApiUserController@user');

        Route::post('logout', 'ApiUserController@logout');
    });

    //Route::middleware('auth:api')->get('/user', 'ApiUserController@user');

});
