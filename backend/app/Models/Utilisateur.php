<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisateur extends Model
{
    use HasFactory;

    protected $fillable = ['Nom', 'Prenom', 'Phone', 'mot_de_passe', 'role'];
}
