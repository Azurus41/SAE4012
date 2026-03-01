<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlinePartieJoueur extends Model
{
    protected $table = 'online_partie_joueurs';

    protected $fillable = [
        'online_partie_id',
        'joueur_id',
        'role',
        'mot',
        'est_elimine',
        'a_parle',
        'ordre',
    ];

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }

    public function partie()
    {
        return $this->belongsTo(OnlinePartie::class, 'online_partie_id');
    }
}
