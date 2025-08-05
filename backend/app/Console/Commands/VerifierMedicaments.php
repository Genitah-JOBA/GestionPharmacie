<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicament;
use App\Models\Notification;
use App\Events\MedicamentNotification;
use Carbon\Carbon;

class VerifierMedicaments extends Command
{
    protected $signature = 'verifier:medicaments';
    protected $description = 'Vérifie les stocks faibles et les dates de péremption';

    public function handle()
    {
        $today = Carbon::today();

        $medicaments = Medicament::all();

        $frequenceFaibleTrouvee = false;

        foreach ($medicaments as $medicament) {
            if ($medicament->frequence_utilisation === 'Faible' && $medicament->quantite < 50) {
                $this->creerNotification(
                    $medicament->nom,
                    "Le médicament {$medicament->nom} présente un stock faible (fréquence faible).",
                    'warning'
                );
            }

            if ($medicament->frequence_utilisation === 'Forte' && $medicament->quantite < 100) {
                $this->creerNotification(
                    $medicament->nom,
                    "Le médicament {$medicament->nom} présente un stock faible (fréquence forte).",
                    'danger'
                );
            }

            if ($medicament->date_expiration) {
                $daysDiff = $today->diffInDays(Carbon::parse($medicament->date_expiration), false);
                if ($daysDiff >= 0 && $daysDiff <= 30) {
                    $this->creerNotification(
                        $medicament->nom,
                        "Le médicament {$medicament->nom} expire bientôt (moins d'un mois).",
                        'danger'
                    );
                } elseif ($daysDiff > 30 && $daysDiff <= 90) {
                    $this->creerNotification(
                        $medicament->nom,
                        "Le médicament {$medicament->nom} expire bientôt (1-3 mois).",
                        'warning'
                    );
                }
            }

            if ($medicament->frequence_utilisation === 'Faible') {
                $frequenceFaibleTrouvee = true;
            }
        }

        if ($frequenceFaibleTrouvee) {
            $this->creerNotification(
                'Médicaments à faible fréquence',
                "Certains médicaments ont une fréquence d'utilisation faible.",
                'info',
                '/medicaments/faible-frequence'
            );
        }

        $this->info('✅ Vérification des médicaments terminée.');
    }

    private function creerNotification($title, $message, $type, $link = null)
    {
        $existe = Notification::where('title', $title)
            ->where('message', $message)
            ->where('read', false)
            ->exists();

        if (!$existe) {
            $notif = Notification::create([
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'link' => $link,
                'read' => false,
            ]);

            broadcast(new MedicamentNotification($notif))->toOthers();
        }
    }
}
