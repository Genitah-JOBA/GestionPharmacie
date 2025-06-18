<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicamentController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API en ligne']);
});

Route::apiResource('medicaments', MedicamentController::class);
Route::get('/medicaments', [MedicamentController::class, 'index']);
Route::post('/medicaments', [MedicamentController::class, 'store']);
Route::delete('/medicaments/{reference}', [MedicamentController::class, 'destroy']);
