<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix(config('app.base_url'))->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/help', 'HomeController@help')->name('help');

    Auth::routes([
        'verify' => true,
    ]);
    Route::get('login/facebook', 'Auth\LoginController@redirectToFacebook')
        ->name('facebookLogin');
    Route::get('login/facebook/callback',
        'Auth\LoginController@handleFacebookCallback');
    Route::get('login/google', 'Auth\LoginController@redirectToGoogle')
        ->name('googleLogin');
    Route::get('login/google/callback',
        'Auth\LoginController@handleGoogleCallback');

    Route::resource('user', 'UserController')->except([
        'index',
        'create',
        'store',
        'show',
    ])->middleware('auth');

    Route::prefix('comic')->name('comic.')->group(function () {
        Route::get('view/{comicId?}/{index?}', 'ComicController@view')
            ->name('view');
        Route::post('request', 'ComicController@request')
            ->name('request');
        Route::get('{comicStrip}/image/{index?}', 'ComicController@getStripImage')
            ->name('image');

        Route::post('{comic}/subscribe', 'ComicController@subscribe')
            ->name('subscribe')
            ->middleware('auth');
        Route::post('{comic}/unsubscribe', 'ComicController@unsubscribe')
            ->name('unsubscribe')
            ->middleware('auth');
    });

    Route::namespace('Admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::middleware('can:admin-user')->group(function () {
                Route::resource('user', 'UserController')->except(['show']);
            });

            Route::middleware('can:admin-comic')->group(function () {
                Route::get('comic/{comic}/add-strip', 'ComicStripController@create')
                    ->name('comicStrip.create');

                Route::post('comic/admin-table-data', 'ComicController@adminTableData')
                    ->name('comic.adminTableData');
                Route::post('comic/admin-table-delete', 'ComicController@adminTableDelete')
                    ->name('comic.adminTableDelete');

                Route::post('comic/strips-admin-table-data/{comic}', 'ComicController@stripsAdminTableData')
                    ->name('comic.stripsAdminTableData');
                Route::post('comic/strips-admin-table-delete', 'ComicController@stripsAdminTableDelete')
                    ->name('comic.stripsAdminTableDelete');

                Route::post('comic/logs-admin-table-data', 'ComicController@logsAdminTableData')
                    ->name('comic.logsAdminTableData');

                Route::resource('comic', 'ComicController');
                Route::get('comicStrip/{comicStrip}/image/{index?}', 'ComicStripController@image')
                    ->name('comicStrip.image');
                Route::get('comicStrip/{comicStrip}/refresh', 'ComicStripController@refresh')
                    ->name('comicStrip.refresh');
                Route::resource('comicStrip', 'ComicStripController')
                    ->except([
                        'create',
                        'index',
                        'show',
                    ]);
            });
        });
});
