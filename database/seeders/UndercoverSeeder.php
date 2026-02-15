<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Mot;
use App\Models\Joueur;
use App\Models\Partie;

class UndercoverSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Mots
        $mots = [
            ['mot_principal' => 'Lion', 'mot_undercover' => 'Tigre', 'categorie' => 'Animaux'],
            ['mot_principal' => 'Pizza', 'mot_undercover' => 'Burger', 'categorie' => 'Nourriture'],
            ['mot_principal' => 'Paris', 'mot_undercover' => 'Londres', 'categorie' => 'Villes'],
            ['mot_principal' => 'Guitare', 'mot_undercover' => 'Violon', 'categorie' => 'Instruments'],
        ];

        foreach ($mots as $m) {
            Mot::create($m);
        }

        // Sample Joueurs
        $joueurs = [
            ['pseudo' => 'Alice', 'email' => 'alice@example.com', 'score_total' => 150],
            ['pseudo' => 'Bob', 'email' => 'bob@example.com', 'score_total' => 120],
            ['pseudo' => 'Charlie', 'email' => 'charlie@example.com', 'score_total' => 90],
            ['pseudo' => 'David', 'email' => 'david@example.com', 'score_total' => 200],
        ];

        $createdJoueurs = [];
        foreach ($joueurs as $j) {
            $createdJoueurs[] = Joueur::create($j);
        }

        // Sample Parties
        $p1 = Partie::create([
            'date' => now()->subDays(2),
            'statut' => 'terminee',
            'mot_id' => 1,
            'gagnant_id' => 1,
        ]);

        $p1->joueurs()->attach($createdJoueurs[0]->id, ['role' => 'civil', 'points_gagnes' => 50]);
        $p1->joueurs()->attach($createdJoueurs[1]->id, ['role' => 'civil', 'points_gagnes' => 50]);
        $p1->joueurs()->attach($createdJoueurs[2]->id, ['role' => 'undercover', 'points_gagnes' => 0]);

        $p2 = Partie::create([
            'date' => now()->subDay(),
            'statut' => 'terminee',
            'mot_id' => 2,
            'gagnant_id' => 4,
        ]);

        $p2->joueurs()->attach($createdJoueurs[3]->id, ['role' => 'civil', 'points_gagnes' => 60]);
        $p2->joueurs()->attach($createdJoueurs[0]->id, ['role' => 'undercover', 'points_gagnes' => 0]);
        $p2->joueurs()->attach($createdJoueurs[1]->id, ['role' => 'mr_white', 'points_gagnes' => 0]);
    }
}
