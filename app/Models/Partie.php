<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partie extends Model
{
    protected $fillable = ['date', 'statut', 'mot_id', 'gagnant_id'];

    public function mot()
    {
        return $this->belongsTo(Mot::class);
    }

    public function gagnant()
    {
        return $this->belongsTo(Joueur::class, 'gagnant_id');
    }

    public function joueurs()
    {
        return $this->belongsToMany(Joueur::class, 'joueur_partie', 'party_id', 'player_id')
                    ->withPivot('role', 'points_gagnes')
                    ->withTimestamps();
    }
}
