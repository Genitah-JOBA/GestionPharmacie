<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['nom_complet'];
    public $timestamps = false;

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }
}
