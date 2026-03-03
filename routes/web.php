<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', function () {
    return view('landing');
});

Route::get('/create-storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link ban gaya! Ab is route ko delete kar dena.';
});