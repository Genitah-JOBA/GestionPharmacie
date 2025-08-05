<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use App\Models\Vente;
use App\Events\MedicamentNotification;
use Carbon\Carbon;

class MedicamentController extends Controller
{
    public function index()
    {
        $medicaments = Medicament::all();
        return response()->json($medicaments, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:10',
            'nom' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
            'date_expiration' => 'required|date',
            'dosage' => 'required|string|max:20',
            'frequence_utilisation' => 'required|in:Forte,Faible',
            'prix_unitaire' => 'required|numeric|min:0',
        ]);

        $lot = Medicament::where('reference', $request->reference)
            ->where('date_expiration', $request->date_expiration)
            ->first();

        if ($lot) {
            $lot->quantite += $request->quantite;
            $lot->prix_unitaire = $request->prix_unitaire;
            $lot->dosage = $request->dosage;
            $lot->frequence_utilisation = $request->frequence_utilisation;
            $lot->save();
        } else {
            $doublons = Medicament::where('reference', $request->reference)->get();

            if ($doublons->count() > 0) {
                $count = $doublons->count();
                $newReference = $request->reference . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);

                Medicament::create([
                    'reference' => $newReference,
                    'nom' => $request->nom,
                    'quantite' => $request->quantite,
                    'date_expiration' => $request->date_expiration,
                    'dosage' => $request->dosage,
                    'frequence_utilisation' => $request->frequence_utilisation,
                    'prix_unitaire' => $request->prix_unitaire,
                ]);
            } else {
                Medicament::create([
                    'reference' => $request->reference,
                    'nom' => $request->nom,
                    'quantite' => $request->quantite,
                    'date_expiration' => $request->date_expiration,
                    'dosage' => $request->dosage,
                    'frequence_utilisation' => $request->frequence_utilisation,
                    'prix_unitaire' => $request->prix_unitaire,
                ]);
            }
        }

        return response()->json(['message' => '✅ Enregistrement réussi !']);
    }

    public function show($reference)
    {
        $medicament = Medicament::find($reference);

        if (!$medicament) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }

        return response()->json($medicament, 200);
        return response()->json(Medicament::all()->map(function ($med) {
            $med->prix_unitaire = (float) $med->prix_unitaire;
            return $med;
        }));
    }

    public function update(Request $request, $reference)
    {
        $medicament = Medicament::where('reference', $reference)->first();

        if (!$medicament) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'quantite' => 'required|integer|min:0',
            'date_expiration' => 'required|date',
            'dosage' => 'required|string|max:100',
            'prix_unitaire' => 'required|numeric|min:0',
            'frequence_utilisation' => 'required|in:Forte,Faible',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exists = Medicament::where('nom', $request->nom)
            ->where('dosage', $request->dosage)
            ->where('reference', '!=', $reference)
            ->exists();

        if ($exists) {
            return response()->json([
                'errors' => ['nom' => ['Un autre médicament avec ce nom et ce dosage existe déjà.']]
            ], 422);
        }

        try {
            $medicament->update($request->all());
            return response()->json([
                'message' => 'Médicament modifié avec succès',
                'medicament' => $medicament
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour médicament : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur interne du serveur'], 500);
        }
    }

    public function destroy($reference)
    {
        $medicament = Medicament::where('reference', $reference)->first();

        if (!$medicament) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }

        $nom = $medicament->nom;
        $frequence = $medicament->frequence_utilisation;
        $dateExpiration = $medicament->date_expiration;

        try {
            $medicament->delete();

            $alerts = [];
            if ($frequence < 50) {
                $alerts[] = 'fréquence faible';
            }

            $now = Carbon::now();
            $dateExp = Carbon::parse($dateExpiration);
            $diffDays = $dateExp->diffInDays($now, false);

            if ($diffDays >= 0 && $diffDays <= 30) {
                $alerts[] = 'expiration proche (<= 1 mois)';
            }

            if (count($alerts) > 0) {
                event(new MedicamentNotification($nom, $frequence, $dateExpiration, $alerts));
            }

            return response()->json(['message' => 'Médicament supprimé avec succès'], 200);
        } catch (\Exception $e) {
            \Log::error('Erreur suppression médicament : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur interne du serveur'], 500);
        }
    }

    public function checkMedicamentsAlerts()
    {
        $today = Carbon::today();
        $alerts = [];
        $medicaments = Medicament::all();
        $ventes = Vente::with('medicaments')->get();
        $usagePerDay = [];

        foreach ($ventes as $vente) {
            foreach ($vente->medicaments as $med) {
                $date = Carbon::parse($vente->created_at)->toDateString();
                $usagePerDay[$med->reference][$date] = ($usagePerDay[$med->reference][$date] ?? 0) + 1;
            }
        }

        $usageAvg = [];
        foreach ($usagePerDay as $ref => $daily) {
            $days = count($daily);
            $total = array_sum($daily);
            $usageAvg[$ref] = $days > 0 ? $total / $days : 0;
        }

        foreach ($medicaments as $med) {
            $medAlerts = [];
            $type = 'info';

            $avg = $usageAvg[$med->reference] ?? 0;
            if (strtolower($med->frequence_utilisation) === 'faible') {
                if ($avg > 10 && $med->quantite < 100) {
                    $medAlerts[] = 'Stock faible (plus utilisé)';
                    $type = 'danger';
                }
                if ($avg <= 10 && $med->quantite < 50) {
                    $medAlerts[] = 'Stock faible (moins utilisé)';
                    $type = 'warning';
                }
            }

            $expiration = Carbon::parse($med->date_expiration);
            $daysToExpire = $today->diffInDays($expiration, false);

            if ($daysToExpire >= 0 && $daysToExpire <= 30) {
                $medAlerts[] = 'Expire 1 mois';
                $type = 'expirationUrgent';
            } elseif ($daysToExpire > 30 && $daysToExpire <= 90) {
                $medAlerts[] = 'Expire 3 mois';
                $type = 'expirationWarning';
            }

            if (count($medAlerts)) {
                $alerts[] = [
                    'reference' => $med->reference,
                    'nom' => $med->nom,
                    'alerts' => $medAlerts,
                    'type' => $type
                ];
            }
        }

        return response()->json([
            'message' => 'Vérification terminée',
            'alerts' => $alerts
        ]);
    }

    public function faibleFrequence()
    {
        $medicaments = Medicament::where('frequence_utilisation', 'Faible')->get();
        if ($medicaments->isEmpty()) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }
        return response()->json($medicaments);
    }
}
