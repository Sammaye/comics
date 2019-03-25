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

Auth::routes();
Route::get('login/facebook', 'Auth\LoginController@redirectToFacebook')->name('facebookLogin');
Route::get('login/facebook/callback', 'Auth\LoginController@handleFacebookCallback');
Route::get('login/google', 'Auth\LoginController@redirectToGoogle')->name('googleLogin');
Route::get('login/google/callback', 'Auth\LoginController@handleGoogleCallback');
