<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CorrigerMotsDePasse extends Command
{
    protected $signature = 'utilisateurs:corriger-motsdepasse';
    protected $description = 'Corrige les mots de passe non hashés des utilisateurs';

    public function handle()
    {
        $this->info("🔐 Vérification et correction des mots de passe non hashés...");

        $corrections = 0;
        $utilisateurs = User::all();

        foreach ($utilisateurs as $user) {
            $info = Hash::info($user->mot_de_passe);
            if ($info['algoName'] !== 'bcrypt') {
                $user->mot_de_passe = Hash::make($user->mot_de_passe);
                $user->save();
                $this->line("✅ Corrigé : {$user->Nom} {$user->Prenom}");
                $corrections++;
            }
        }

        $this->info("✔️ Correction terminée. Total : $corrections utilisateur(s) mis à jour.");
    }
}
