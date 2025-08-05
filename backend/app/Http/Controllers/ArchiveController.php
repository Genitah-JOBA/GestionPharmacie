<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    public function archives(Request $request)
    {
        $query = DB::table('ventes')
            ->join('clients', 'ventes.client_id', '=', 'clients.id')
            ->join('vente_medicament', 'ventes.id', '=', 'vente_medicament.vente_id')
            ->select(
                'clients.nom_complet',
                DB::raw('COUNT(DISTINCT ventes.id) as nombre_ventes'),
                DB::raw('COALESCE(SUM(ventes.montant_total), 0) as montant_total'),
                DB::raw('MAX(ventes.created_at) as derniere_vente')
            )
            ->groupBy('clients.nom_complet');

        if ($request->filled('search')) {
            $query->where('clients.nom_complet', 'LIKE', '%' . $request->input('search') . '%');
        }

        $perPage = $request->input('per_page', 10);
        $clients = $query->paginate($perPage);

        return response()->json([
            'data' => $clients->items(),
            'total' => $clients->total(),
            'chiffreAffaires' => DB::table('ventes')->sum('montant_total'),
        ]);
    }

    public function archivesParClient($nomComplet)
    {
        $rawVentes = DB::table('vente_medicament')
            ->join('ventes', 'vente_medicament.vente_id', '=', 'ventes.id')
            ->join('medicament', 'vente_medicament.medicament_id', '=', 'reference')
            ->join('clients', 'ventes.client_id', '=', 'clients.id')
            ->where('clients.nom_complet', $nomComplet)
            ->select(
                'ventes.id as vente_id',
                'ventes.created_at as date',
                'ventes.mode_paiement',
                'ventes.montant_partiel',
                'medicament.nom as nom_medicament',
                'vente_medicament.quantite',
                'vente_medicament.sous_total',
                'clients.id as client_id'
            )
            ->orderBy('ventes.created_at', 'desc')
            ->get();

        $ventes = [];
        foreach ($rawVentes as $item) {
            $venteId = $item->vente_id;
            if (!isset($ventes[$venteId])) {
                $ventes[$venteId] = [
                    'vente_id' => $venteId,
                    'date' => $item->date,
                    'mode_paiement' => $item->mode_paiement,
                    'montant_partiel' => $item->montant_partiel,
                    'sous_total' => 0,
                    'produits' => [],
                ];
            }

            $ventes[$venteId]['produits'][] = [
                'nom' => $item->nom_medicament,
                'quantite' => $item->quantite,
                'montant' => $item->sous_total,
            ];

            $ventes[$venteId]['sous_total'] += $item->sous_total;
        }

        $clientId = $rawVentes->first()->client_id ?? null;

        return response()->json([
            'ventes' => array_values($ventes),
            'total' => collect($rawVentes)->sum('sous_total'),
            'client_id' => $clientId
        ]);
    }

    public function payerPartiel(Request $request)
    {
        $venteId = $request->input('vente_id');
        $montant = $request->input('montant');

        if (!$venteId || !$montant) {
            return response()->json(['error' => 'Données manquantes'], 400);
        }

        DB::table('ventes')
            ->where('id', $venteId)
            ->update([
                'mode_paiement' => 'Paiement partiel',
                'montant_partiel' => $montant,
            ]);

        return response()->json(['message' => 'Paiement partiel enregistré ✅']);
    }

    public function payerComplet($client, Request $request)
    {
        $date = $request->input('date');

        DB::table('ventes')
            ->where('client_id', $client)
            ->whereDate('created_at', $date)
            ->update([
                'mode_paiement' => 'Payé',
                'montant_partiel' => null
            ]);

        return response()->json(['message' => 'Paiement complet enregistré ✅']);
    }
}
