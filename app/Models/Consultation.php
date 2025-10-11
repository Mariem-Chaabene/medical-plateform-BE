<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;
     protected $fillable = [
        'dme_id', 'medecin_id', 'date_consultation', 'diagnostic', 'traitement'
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
}
