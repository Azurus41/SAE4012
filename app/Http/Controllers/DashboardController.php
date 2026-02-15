<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mot;
use App\Models\Joueur;
use App\Models\Partie;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'mots_count' => Mot::count(),
            'joueurs_count' => Joueur::count(),
            'parties_count' => Partie::count(),
            'recent_parties' => Partie::with(['mot', 'gagnant'])->latest()->take(5)->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
