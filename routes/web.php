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

Route::fallback(function (\Illuminate\Http\Request $request) {
    if ($request->is('api/*') || $request->wantsJson()) {
        return response()->json([
            'success' => false,
            'message' => "The route {$request->path()} could not be found."
        ], 404);
    }
    return view('notes');
});
