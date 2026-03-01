<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineMessage extends Model
{
    protected $table = 'online_messages';
    protected $fillable = ['online_partie_id', 'joueur_id', 'contenu'];

    public function joueur()
    {
        return $this->belongsTo(Joueur::class);
    }
}
