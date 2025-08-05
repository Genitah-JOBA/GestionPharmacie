<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicament;
use App\Models\VenteMedicament;
use App\Models\Notification;

class CheckMedicament extends Command
{
    protected $signature = 'medicament:check';
    protected $description = 'Vérifie les stocks et dates des médicaments pour générer des notifications';

    public function handle()
    {
        $medicaments = Medicament::all();

        foreach ($medicaments as $med) {
            $joursAvantExpiration = now()->diffInDays($med->date_expiration, false);
            $ventes = VenteMedicament::where('medicament_id', $med->reference)->count();
            $stock = $med->quantite;

            $type = null;
            $causes = [];

            if ($joursAvantExpiration <= 30) {
                $causes[] = 'expiration proche';
                $type = 'danger';
            }
            if ($joursAvantExpiration <= 90 && $joursAvantExpiration >= 60) {
                $causes[] = 'expiration à moyen terme';
                $type = 'warning';
            }

            if ($stock < 100 && $ventes > 10) {
                $causes[] = 'stock faible (fréquence élevée)';
                $type = 'danger';
            }
            if ($stock < 50 && $ventes <= 10) {
                $causes[] = 'stock faible (fréquence faible)';
                $type = 'warning';
            }

            if (empty($causes)) {
                continue;
            }

            $message = "Le médicament {$med->nom} présente une alerte : " . implode(', ', $causes) . ".";

            if (!Notification::where('message', $message)->where('read', false)->exists()) {
                Notification::create([
                    'title' => 'Alerte Médicament',
                    'message' => $message,
                    'type' => $type ?? 'info',
                    'read' => false,
                ]);
            }
        }

        $this->info('Vérification terminée ✅');
    }
}
