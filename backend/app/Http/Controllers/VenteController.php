<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use App\Models\VenteMedicament;
use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\MedicamentNotification;

class VenteController extends Controller
{
    public function validerVentes(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'client_nom' => 'required|string',
                'mode_paiement' => 'required|string|in:Espèces,Mobile Money,Virement,Paiement en attente',
                'panier' => 'required|array|min:1',
                'panier.*.reference' => 'required|string|exists:medicament,reference',
                'panier.*.quantite' => 'required|integer|min:1',
            ]);

            $client = Client::firstOrCreate(['nom_complet' => $validated['client_nom']]);

            $vente = Vente::create([
                'client_id' => $client->id,
                'mode_paiement' => $validated['mode_paiement'],
                'montant_total' => 0,
                'code' => uniqid('VTE_'),
                'user_id' => auth()->id() ?? 1,
            ]);

            $total = 0;

            foreach ($validated['panier'] as $item) {
                $medicament = Medicament::where('reference', $item['reference'])->firstOrFail();

                if ($medicament->quantite < $item['quantite']) {
                    return response()->json(['message' => "Stock insuffisant pour {$medicament->nom}"], 400);
                }

                $medicament->decrement('quantite', $item['quantite']);

                $sous_total = $item['quantite'] * $medicament->prix_unitaire;

                VenteMedicament::create([
                    'vente_id' => $vente->id,
                    'medicament_id' => $medicament->reference,
                    'quantite' => $item['quantite'],
                    'prix_unitaire' => $medicament->prix_unitaire,
                    'sous_total' => $sous_total,
                ]);

                $total += $sous_total;

                if ($medicament->expiration_date) {
                    $today = now();
                    if ($medicament->expiration_date <= $today->copy()->addDays(14)) {
                        broadcast(new MedicamentNotification([
                            'id' => time(),
                            'title' => 'Expiration très proche',
                            'message' => 'Le médicament "' . $medicament->nom . '" expire dans 2 semaines.',
                            'type' => 'info',
                            'date' => now()->toIso8601String(),
                            'read' => false,
                        ]));
                    } elseif ($medicament->expiration_date <= $today->copy()->addDays(30)) {
                        broadcast(new MedicamentNotification([
                            'id' => time() + 1,
                            'title' => 'Expiration prochaine',
                            'message' => 'Le médicament "' . $medicament->nom . '" expire dans 1 mois.',
                            'type' => 'danger',
                            'date' => now()->toIso8601String(),
                            'read' => false,
                        ]));
                    }
                }
            }

            $medocPlusUtilise = Medicament::orderByDesc('frequence_utilisation')->first();
            if ($medocPlusUtilise && $medocPlusUtilise->frequence_utilisation < 100) {
                broadcast(new MedicamentNotification([
                    'id' => time() + 2,
                    'title' => 'Fréquence faible (plus utilisé)',
                    'message' => 'Le médicament "' . $medocPlusUtilise->nom . '" a une fréquence faible.',
                    'type' => 'danger',
                    'date' => now()->toIso8601String(),
                    'read' => false,
                ]));
            }

            $medocMoinsUtilise = Medicament::orderBy('frequence_utilisation')->first();
            if ($medocMoinsUtilise && $medocMoinsUtilise->frequence_utilisation < 50) {
                broadcast(new MedicamentNotification([
                    'id' => time() + 3,
                    'title' => 'Fréquence très faible (moins utilisé)',
                    'message' => 'Le médicament "' . $medocMoinsUtilise->nom . '" a une fréquence très faible.',
                    'type' => 'warning',
                    'date' => now()->toIso8601String(),
                    'read' => false,
                ]));
            }

            $vente->update(['montant_total' => $total]);

            DB::commit();

            return response()->json([
                'message' => '✅ Vente enregistrée avec succès',
                'vente_id' => $vente->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e);

            return response()->json([
                'message' => '❌ Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }
}
