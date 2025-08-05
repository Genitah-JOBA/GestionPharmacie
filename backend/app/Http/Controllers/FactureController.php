<?php

namespace App\Http\Controllers;

use App\Models\Vente;

class FactureController extends Controller
{
    public function show($id)
    {
        $vente = Vente::with(['client', 'medicaments.medicament'])->findOrFail($id);

        $facture = [
            'id' => $vente->id,
            'nomClient' => $vente->client->nom_complet,
            'modePaiement' => $vente->mode_paiement,
            'total' => number_format($vente->montant_total, 2),
            'items' => $vente->medicaments->map(function ($item) {
                return [
                    'nom' => $item->medicament->nom,
                    'dosage' => $item->medicament->dosage ?? '',
                    'quantite' => $item->quantite,
                    'prix' => $item->prix_unitaire,
                    'sousTotal' => number_format($item->sous_total, 2),
                ];
            }),
        ];

        return response()->json(['facture' => $facture]);
    }
}
