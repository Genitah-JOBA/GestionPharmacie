<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MedicamentController extends Controller
{
    public function index()
    {
        return Medicament::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'reference' => 'required|unique:medicament',
            'nom' => 'required',
            'quantite' => 'required|integer',
            'date_expiration' => 'required|date',
            'dosage' => 'required',
        ]);

        $exists = Medicament::where('nom', $request->nom)
            ->where('dosage', $request->dosage)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nom' => 'Un médicament avec ce nom et ce dosage existe déjà.',
            ]);
        }

        return Medicament::create($request->all());
    }

    public function show($reference)
    {
        return Medicament::findOrFail($reference);
    }

    public function update(Request $request, $reference)
    {
        $medicament = Medicament::findOrFail($reference);

        $request->validate([
            'nom' => 'required',
            'quantite' => 'required|integer',
            'date_expiration' => 'required|date',
            'dosage' => 'required',
        ]);

        $exists = Medicament::where('nom', $request->nom)
            ->where('dosage', $request->dosage)
            ->where('reference', '!=', $reference)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nom' => 'Un autre médicament avec ce nom et ce dosage existe déjà.',
            ]);
        }

        $medicament->update($request->all());
        return $medicament;
    }

    public function destroy($reference)
    {
        $medicament = Medicament::findOrFail($reference);
        $medicament->delete();
        return response()->json(['message' => 'Supprimé avec succès']);
    }
}
