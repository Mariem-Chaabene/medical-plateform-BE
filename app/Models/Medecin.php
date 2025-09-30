<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'specialite', 'numero_inscription'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
