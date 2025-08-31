<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infirmier extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'sexe', 'numero_inscription', 'departement', 'telephone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
