<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mot extends Model
{
    protected $fillable = ['mot_principal', 'mot_undercover', 'categorie', 'image'];

    public function parties()
    {
        return $this->hasMany(Partie::class);
    }
}
