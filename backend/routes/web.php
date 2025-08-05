<?php

use App\Events\MedicamentNotification;
use App\Http\Controllers\MedicamentController;

Route::get('/check-medicaments-alerts', [MedicamentController::class, 'checkMedicamentsAlerts']);

Route::get('/test-broadcast', function () {
    broadcast(new MedicamentNotification([
        'nom' => 'TestParacetamol',
        'alerts' => ['Date expirée', 'Stock critique'],
    ]));

    return 'Notification broadcast envoyée !';
});
