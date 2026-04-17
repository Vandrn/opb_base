<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'OPB API';
});

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
