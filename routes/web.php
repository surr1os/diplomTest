<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/token', function () {
    return response()->json(['token' => csrf_token()]);
});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
