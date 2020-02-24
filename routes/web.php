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

Route::get('/', ['uses' => 'Auth\LoginController@loginPage'])->name('/');

//Authentication related urls
Route::get('/login', ['uses' => 'Auth\LoginController@loginPage'])->name('login');
Route::get('/logout', ['uses' => 'Auth\LoginController@logout'])->name('logout');
Route::get('/reset-password/{code?}', ['uses' => 'Auth\PasswordController@resetPassPage'])->name('reset-password');
Route::get('/forgot-password', ['uses' => 'Auth\PasswordController@forgotPass'])->name('forgot-password');
Route::post('/forgot-password', ['uses' => 'Auth\PasswordController@forgotPass'])->name('forgot-password');
Route::post('/login-post', ['uses' => 'Auth\LoginController@loginPost'])->name('login-post');
Route::post('/reset-password/{code?}', ['uses' => 'Auth\PasswordController@resetPassPage'])->name('reset-password');
