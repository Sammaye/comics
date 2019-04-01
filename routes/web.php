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
]);

Route::prefix('comic')->name('comic.')->group(function () {
    Route::get('view', 'ComicController@view')->name('index');
    Route::get('view/{comic}', 'ComicController@view')->name('view');
    Route::get('{comic}/subscribe', 'ComicController@subscribe')->name('subscribe');
    Route::get('{comic}/unsubscribe', 'ComicController@unsubscribe')->name('unsubscribe');
    Route::post('request', 'ComicController@request')->name('request');
    Route::get('{strip}/image', 'ComicController@getStripImage')->name('image');
});

Route::namespace('Admin')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('can:admin-user')->group(function () {
            Route::resource('user', 'UserController')->except(['show']);
        });

        Route::middleware('can:admin-comic')->group(function () {
            Route::resource('comic', 'ComicController');
        });
    });
