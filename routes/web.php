<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('tryit');
});

Route::get('/landing_page', function () {
    return view('landing_page');
});

Route::get('/footer', function () {
    return view('layouts.footer');
});

// Route::get('/', function () {
//     return view('tryit');
// });

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
});

