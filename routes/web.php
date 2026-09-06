<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Primary Web Entrypoint serving AI Notes SPA Frontend
|
*/

Route::get('/', function () {
    return view('notes');
});
