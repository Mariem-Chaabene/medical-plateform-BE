<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TypeAnalyse;
class Analyse extends Model
{
    use HasFactory;
    protected $fillable = ['dme_id', 'consultation_id','type_analyse_id', 'date_analyse', 'resultat', 'remarques'];
    protected $casts = ['date_analyse' => 'datetime'];
    
    public function dme()
    {
        return $this->belongsTo(Dme::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
    
    public function typeAnalyse()
    {
        return $this->belongsTo(TypeAnalyse::class, 'type_analyse_id');
    }
}
