<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\Medicament;
use App\Models\Vente;
use App\Models\User;

class PharmacienController extends Controller
{
    public function stats()
    {
        try {
            $today = Carbon::today();
            $ventesJournalieres = Vente::whereDate('created_at', $today)->sum('montant_total');
            $ventesTotales = Vente::sum('montant_total');
            $medicamentsPerimes = Medicament::where('date_expiration', '<', Carbon::today())->count();
            $medicaments = Medicament::count();
            $utilisateurs = User::count();
            $achatsJournaliers = Vente::selectRaw('DATE(created_at) as jour, SUM(montant_total) as total')
                ->groupBy('jour')
                ->orderBy('jour', 'asc')
                ->get();
            $ruptures_stock = Medicament::where('quantite', '<=', 50)->count();

            return response()->json([
                'medicaments' => $medicaments,
                'ventes_journalieres' => $ventesJournalieres,
                'ventes_total' => $ventesTotales,
                'medicaments_perimes' => $medicamentsPerimes,
                'utilisateurs' => $utilisateurs,
                'achats_journaliers' => $achatsJournaliers,
                'ruptures_stock' => $ruptures_stock,
            ]);
        } catch (\Exception $e) {
            \Log::error("Erreur dans stats() : " . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du chargement des statistiques'], 500);
        }
    }
}
