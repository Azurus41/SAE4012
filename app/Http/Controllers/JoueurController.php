<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Joueur;
use Illuminate\Support\Facades\Storage;

class JoueurController extends Controller
{
    public function index()
    {
        $joueurs = Joueur::all();
        return view('joueurs.index', compact('joueurs'));
    }

    public function create()
    {
        return view('joueurs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pseudo' => 'required|string|max:255',
            'email' => 'required|email|unique:joueurs,email',
            'score_total' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        Joueur::create($validated);

        return redirect()->route('joueurs.index')->with('success', 'Joueur créé avec succès.');
    }

    public function show(Joueur $joueur)
    {
        $joueur->load('parties');
        return view('joueurs.show', compact('joueur'));
    }

    public function edit(Joueur $joueur)
    {
        return view('joueurs.edit', compact('joueur'));
    }

    public function update(Request $request, Joueur $joueur)
    {
        $validated = $request->validate([
            'pseudo' => 'required|string|max:255',
            'email' => 'required|email|unique:joueurs,email,' . $joueur->id,
            'score_total' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($joueur->avatar) {
                Storage::disk('public')->delete($joueur->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $joueur->update($validated);

        return redirect()->route('joueurs.index')->with('success', 'Joueur mis à jour avec succès.');
    }

    public function destroy(Joueur $joueur)
    {
        if ($joueur->avatar) {
            Storage::disk('public')->delete($joueur->avatar);
        }
        $joueur->delete();

        return redirect()->route('joueurs.index')->with('success', 'Joueur supprimé avec succès.');
    }
}
