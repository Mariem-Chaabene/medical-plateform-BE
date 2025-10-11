<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AntecedentMedical extends Model
{
    use HasFactory;

    protected $fillable = ['dme_id', 'nom_maladie', 'date_diagnostic', 'remarques'];
    protected $casts = ['date_diagnostic' => 'date'];

    public function dme()
    {
        return $this->belongsTo(Dme::class);
    }
}
