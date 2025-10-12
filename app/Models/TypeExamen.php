<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeExamen extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'libelle'];
    
    public const CODES = [
        'BIO', // Biologie
        'RAD', // Radiographie
        'SCAN',
        'IRM',
        'ECG'
    ];

    public function examens()
    {
        return $this->hasMany(Examen::class);
    }
}
