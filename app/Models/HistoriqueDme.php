<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class HistoriqueDme extends Model
{
    use HasFactory;
    protected $table = 'historique_dme';
    protected $fillable = ['dme_id', 'user_id', 'action', 'old', 'new'];
    protected $casts = ['old' => 'array', 'new' => 'array'];
    public function dme()
    {
        return $this->belongsTo(Dme::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
