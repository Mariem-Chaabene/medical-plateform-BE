<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAnalyse extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = ['libelle'];
    
    public function analyses()
    {
        return $this->hasMany(Analyse::class, 'type_analyse_id');
    }
}
