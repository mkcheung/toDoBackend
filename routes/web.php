<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/sanctum/csrf-cookie', fn () => response()->noContent());
