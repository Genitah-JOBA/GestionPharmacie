<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Medicament extends Model
{
    protected $table = 'medicament';
    protected $primaryKey = 'reference';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'reference',
        'nom',
        'quantite',
        'date_expiration',
        'dosage',
        'frequence_utilisation',
        'prix_unitaire',
    ];

    protected $casts = [
        'date_expiration' => 'date:Y-m-d',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected function dateExpiration(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
            set: fn ($value) => $value ? Carbon::parse($value) : null,
        );
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static function booted()
    {
        static::saving(function ($med) {
            $med->frequence_utilisation = $med->quantite >= 100 ? 'Forte' : 'Faible';
        });
    }

    public function ventes()
    {
        return $this->belongsToMany(Vente::class, 'vente_medicament', 'medicament_id', 'vente_id')
            ->withPivot('quantite', 'prix_unitaire', 'sous_total')
            ->withTimestamps();
    }
}
