<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Partie;
use App\Models\Mot;
use App\Models\Joueur;

class PartieController extends Controller
{
    public function index()
    {
        $parties = Partie::with(['mot', 'gagnant'])->get();
        return view('parties.index', compact('parties'));
    }

    public function create()
    {
        $mots = Mot::all();
        $joueurs = Joueur::all();
        return view('parties.create', compact('mots', 'joueurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'statut' => 'required|string',
            'mot_id' => 'required|exists:mots,id',
            'gagnant_id' => 'nullable|exists:joueurs,id',
            'joueurs' => 'nullable|array',
            'joueurs.*' => 'exists:joueurs,id',
            'roles' => 'nullable|array',
            'points' => 'nullable|array',
        ]);

        $partie = Partie::create($validated);

        if ($request->has('joueurs')) {
            foreach ($request->joueurs as $index => $playerId) {
                $partie->joueurs()->attach($playerId, [
                    'role' => $request->roles[$index] ?? 'civil',
                    'points_gagnes' => $request->points[$index] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('parties.index')->with('success', 'Partie créée avec succès.');
    }

    public function show(Partie $party)
    {
        $party->load(['mot', 'gagnant', 'joueurs']);
        return view('parties.show', compact('party'));
    }

    public function edit(Partie $party)
    {
        $mots = Mot::all();
        $joueurs = Joueur::all();
        $party->load('joueurs');
        return view('parties.edit', compact('party', 'mots', 'joueurs'));
    }

    public function update(Request $request, Partie $party)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'statut' => 'required|string',
            'mot_id' => 'required|exists:mots,id',
            'gagnant_id' => 'nullable|exists:joueurs,id',
            'joueurs' => 'nullable|array',
            'joueurs.*' => 'exists:joueurs,id',
            'roles' => 'nullable|array',
            'points' => 'nullable|array',
        ]);

        $party->update($validated);

        if ($request->has('joueurs')) {
            $syncData = [];
            foreach ($request->joueurs as $index => $playerId) {
                $syncData[$playerId] = [
                    'role' => $request->roles[$index] ?? 'civil',
                    'points_gagnes' => $request->points[$index] ?? 0,
                ];
            }
            $party->joueurs()->sync($syncData);
        } else {
            $party->joueurs()->detach();
        }

        return redirect()->route('parties.index')->with('success', 'Partie mise à jour avec succès.');
    }

    public function destroy(Partie $party)
    {
        $party->delete();
        return redirect()->route('parties.index')->with('success', 'Partie supprimée avec succès.');
    }
}
