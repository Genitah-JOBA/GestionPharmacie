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
    ];

    protected $casts = [
        'date_expiration' => 'date',
    ];

    protected function dateExpiration(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-m-d'),
        );
    }

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d_H:i:s');
    }

    protected static function booted()
    {
        static::saved(function () {
            $maxQuantite = self::max('quantite');
            $plusUtilise = self::where('quantite', $maxQuantite)->first();

            if($plusUtilise){
                $plusUtilise->frequence_utilisation = $plusUtilise->quantite > 100 ? 'Forte' : 'Faible';
                $plusUtilise->saveQuietly();
            }

            self::where('reference', '<>', optional($plusUtilise)->reference)->get()->each(function($med){
                $med->frequence_utilisation = $med->quantite < 50 ? 'Faible' : 'Forte';
                $med->saveQuietly();
            });
        });
    }
}
