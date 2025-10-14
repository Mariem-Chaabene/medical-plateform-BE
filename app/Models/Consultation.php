<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;
    protected $fillable = [
        'dme_id',
        'medecin_id',
        'date_consultation',
        'diagnostic',
        'traitement',
        'motif',
        'poids',
        'taille',
        'imc',
        'temperature',
        'frequence_cardiaque',
        'pression_arterielle'
    ];

    public function dme()
    {
        return $this->belongsTo(Dme::class);
    }

    public function medecin()
    {
        return $this->belongsTo(User::class, 'medecin_id');
    }

    public function examens()
    {
        return $this->hasMany(Examen::class);
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class);
    }
}
