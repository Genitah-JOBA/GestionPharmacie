<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicamentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PharmacienController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API en ligne']);
});

Route::get('/notifications', function () {
    return \App\Models\Notification::latest()->get();
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/doit_changer_mdp', [AuthController::class, 'changePassword']);
    Route::get('/users', [AdminController::class, 'index']);
    Route::put('/users/{id}', [AuthController::class, 'update']);
    Route::delete('/users/{id}', [AuthController::class, 'destroy']);
    Route::put('/users/{id}/toggle', [AdminController::class, 'toggleStatus']);
    Route::get('/pharmacien/stats', [PharmacienController::class, 'stats']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/facture/{id}', [VenteController::class, 'afficherFacture']);
    Route::get('/medicaments', [MedicamentController::class, 'index']);
});
Route::get('/medicaments/faible-frequence', [MedicamentController::class, 'faibleFrequence']);
Route::post('/ventes', [VenteController::class, 'validerVentes']);
Route::apiResource('medicaments', MedicamentController::class);
Route::get('/factures/{id}', [FactureController::class, 'show']);

Route::get('/ventes/archives', [ArchiveController::class, 'archives']);
Route::get('/archives/ventes', [ArchiveController::class, 'archives']);
Route::get('/archives/ventes/{nomComplet}', [ArchiveController::class, 'archivesParClient']);

Route::get('/notifications', [NotificationController::class, 'index']);
Route::post('/notifications', [NotificationController::class, 'store']);
Route::put('/notifications/{id}/read', [NotificationController::class, 'markRead']);
Route::put('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
Route::post('/archives/ventes/{client}/payer-complet', [ArchiveController::class, 'payerComplet']);
Route::post('/archives/ventes/payer-partiel', [ArchiveController::class, 'payerPartiel']);

Route::post('/password/forgot-password', [PasswordResetController::class, 'sendOtp']);
Route::post('/password/verify-code', [PasswordResetController::class, 'verifyOtp']);
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
