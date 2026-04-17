<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisitController;

// Rutas públicas
Route::get('/countries', [VisitController::class, 'getCountries']);
Route::get('/stores', [VisitController::class, 'getStores']);

// Rutas de visitas
Route::post('/visits', [VisitController::class, 'createVisit']);
Route::get('/visits/{id}', [VisitController::class, 'getVisit']);
Route::patch('/visits/{id}', [VisitController::class, 'updateVisit']);
