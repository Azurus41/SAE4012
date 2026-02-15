<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Joueur extends Model
{
    protected $fillable = ['pseudo', 'email', 'score_total', 'avatar'];

    public function parties()
    {
        return $this->belongsToMany(Partie::class, 'joueur_partie', 'player_id', 'party_id')
                    ->withPivot('role', 'points_gagnes')
                    ->withTimestamps();
    }

    public function partiesGagnees()
    {
        return $this->hasMany(Partie::class, 'gagnant_id');
    }
}
