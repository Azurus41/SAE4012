<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineVote extends Model
{
    protected $table = 'online_votes';
    protected $fillable = ['online_partie_id', 'votant_id', 'cible_id', 'tour_numero'];
}
