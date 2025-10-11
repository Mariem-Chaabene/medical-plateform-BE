<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraitementChronique extends Model
{
    use HasFactory;

    protected $fillable = [
        'dme_id',
        'nom_medicament',
        'dosage',
        'frequence',
        'date_debut',
        'date_fin',
        'is_active',
        'remarques'
    ];

    protected $casts = ['is_active' => 'boolean', 'date_debut' => 'date', 'date_fin' => 'date'];

    public function dme()
    {
        return $this->belongsTo(Dme::class);
    }
}
