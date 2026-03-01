<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlinePartie extends Model
{
    protected $fillable = [
        'code',
        'statut',
        'mot_id',
        'mot_civil',
        'mot_undercover',
        'joueur_actuel_index',
        'phase_vote_id',
        'timer_expiry',
        'tour_numero',
    ];

    protected $casts = [
        'timer_expiry' => 'datetime',
    ];

    public function joueurs()
    {
        return $this->hasMany(OnlinePartieJoueur::class)->orderBy('ordre');
        
    }

    public function joueursActifs()
    {
        return $this->hasMany(OnlinePartieJoueur::class)->where('est_elimine', false)->orderBy('ordre');
    }

    public function messages()
    {
        return $this->hasMany(OnlineMessage::class)->orderBy('created_at');
    }

    public function votes()
    {
        return $this->hasMany(OnlineVote::class);
    }

    public function mot()
    {
        return $this->belongsTo(Mot::class);
    }
}
