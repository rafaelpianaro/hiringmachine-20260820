<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to Laravel with Docker!',
        'status' => 'running',
        'port' => env('APP_PORT', 5757)
    ]);
});

Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});
