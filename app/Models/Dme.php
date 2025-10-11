<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dme extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'groupe_sanguin',
        'notes_medicales',
    ];

    public const GROUPES_SANGUINS = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function traitements()
    {
        return $this->hasMany(TraitementChronique::class);
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class);
    }
    
    //Cela permet d’accéder à tous les examens d’un DME sans passer explicitement par les consultations.
    public function examens()
    {
        return $this->hasManyThrough(Examen::class, Consultation::class);
    }
}


