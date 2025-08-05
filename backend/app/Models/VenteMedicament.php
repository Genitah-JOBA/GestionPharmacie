<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenteMedicament extends Model
{
    protected $table = 'Vente_Medicament';
    protected $fillable = ['vente_id', 'medicament_id', 'quantite', 'prix_unitaire', 'sous_total'];

    public function medicament()
    {
        return $this->belongsTo(Medicament::class, 'medicament_id', 'reference');
    }
}
