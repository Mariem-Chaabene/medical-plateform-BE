<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;

    protected $fillable = ['consultation_id', 'type_examen_id', 'date_examen', 'etat', 'resultat', 'remarques'];
    protected $casts = ['date_examen' => 'datetime'];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }


    public function type()
    {
        return $this->belongsTo(TypeExamen::class, 'type_examen_id');
    }
}
