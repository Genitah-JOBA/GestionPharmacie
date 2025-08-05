<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'Nom' => 'JOBA',
                'Prenom' => 'Razafindrasoa Genitah',
                'Phone' => '0381461105',
                'mot_de_passe' => '$2y$12$949HM4jw6c6l9xDmPFIYDeqjZImfQBoMx/6/sNiaL7994gjH70t56',
                'role' => 'Pharmacien',
                'actif' => 1,
                'doit_changer_mdp' => 0,
                'derniere_connexion' => '2025-06-30 13:46:55',
                'created_at' => '2025-06-18 09:35:40',
                'updated_at' => '2025-06-30 13:46:55',
            ],
            [
                'id' => 2,
                'Nom' => 'Admin',
                'Prenom' => 'Principal',
                'Phone' => '0341234567',
                'mot_de_passe' => '$2y$10$e0NRERLUf/ziwNhJXkDMEu5WIGIQNTH/S5DO9u8MLwU/Np7hsGHn6',
                'role' => 'Administrateur',
                'actif' => 1,
                'doit_changer_mdp' => 0,
                'derniere_connexion' => null,
                'created_at' => '2025-06-18 10:32:46',
                'updated_at' => '2025-06-18 10:32:46',
            ],
            [
                'id' => 3,
                'Nom' => 'ANJARASOA',
                'Prenom' => 'Martinie Anniah Lorraine',
                'Phone' => '0347591421',
                'mot_de_passe' => '$2y$12$p8Cn6TPV445Qfy0p913IJ.PPkEGGQl8KQw2c.Fic9BfMJ6teRo3OK',
                'role' => 'Administrateur',
                'actif' => 1,
                'doit_changer_mdp' => 0,
                'derniere_connexion' => '2025-06-30 13:52:24',
                'created_at' => '2025-06-18 10:34:26',
                'updated_at' => '2025-06-30 13:52:24',
            ],
        ]);
    }
}
