<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = ['code', 'montant_total', 'user_id', 'client_id', 'mode_paiement', 'date_vente'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function medicaments()
    {
        return $this->hasMany(VenteMedicament::class);
    }
}
