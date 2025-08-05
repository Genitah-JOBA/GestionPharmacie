<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Nom' => 'required|string|max:255',
            'Prenom' => 'required|string|max:255',
            'Phone' => 'required|digits:10|unique:users,Phone',
            'mot_de_passe' => 'required|string|min:6',
            'role' => 'required|in:Administrateur,Pharmacien',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'Nom' => $request->Nom,
            'Prenom' => $request->Prenom,
            'Phone' => $request->Phone,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Utilisateur enregistré avec succès',
            'token' => $token,
            'user' => $user->makeHidden('id', 'mot_de_passe')
        ]);
    }

    public function login(Request $request)
    {
        \Log::info('Tentative login avec données : ' . json_encode($request->all()));

        $request->validate([
            'Nom' => 'required|string',
            'Phone' => 'required|string',
            'mot_de_passe' => 'required|string',
        ]);

        $user = User::where('Nom', $request->Nom)
                    ->where('Phone', $request->Phone)
                    ->first();

        if (!$user) {
            \Log::warning('Utilisateur non trouvé');
            return response()->json(['message' => 'Informations incorrectes'], 401);
        }

        try {
            if (!\Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
                \Log::warning('Mot de passe incorrect');
                return response()->json(['message' => 'Informations incorrectes'], 401);
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la vérification du mot de passe : ' . $e->getMessage());
            return response()->json([
                'message' => 'Problème avec la sécurité du mot de passe, contactez l\'administrateur.'
            ], 500);
        }

        if (!$user->actif) {
            \Log::warning('Compte inactif');
            return response()->json(['message' => 'Compte inactif.'], 403);
        }

        $payloadRole = $user->role === 'Administrateur' ? 'admin' : 'pharmacien';

        $user->derniere_connexion = now();
        $user->save();

        \Log::info('Connexion réussie pour utilisateur ' . $user->id);

        $token = $user->createToken('pharma-token')->plainTextToken;
        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'doit_changer_mdp' => $user->doit_changer_mdp,
            'role' => $payloadRole,
            'user' => $user->makeHidden('id', 'mot_de_passe'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function index()
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des utilisateurs : ' . $e->getMessage());
            return response()->json(['message' => 'Erreur interne du serveur'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'Nom' => 'required|string|max:255',
            'Prenom' => 'nullable|string|max:255',
            'Phone' => 'required|digits:10|unique:users,Phone,' . $user->id,
            'role' => 'required|in:Administrateur,Pharmacien',
            'mot_de_passe' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->Nom = $request->Nom;
        $user->Prenom = $request->Prenom;
        $user->Phone = $request->Phone;
        $user->role = $request->role;

        if ($request->filled('mot_de_passe')) {
            $user->mot_de_passe = Hash::make($request->mot_de_passe);
        }

        $user->save();

        return response()->json(['message' => 'Utilisateur modifié avec succès']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->mot_de_passe)) {
            return response()->json(['message' => 'Ancien mot de passe incorrect'], 400);
        }

        $user->mot_de_passe = Hash::make($request->new_password);
        $user->doit_changer_mdp = false;
        $user->save();

        return response()->json(['message' => 'Mot de passe modifié avec succès']);
    }
}
