<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->actif = !$user->actif;
        $user->save();

        return response()->json(['message' => 'Statut mis à jour']);
    }
}
