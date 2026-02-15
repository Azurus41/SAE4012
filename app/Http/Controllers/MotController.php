<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mot;
use Illuminate\Support\Facades\Storage;

class MotController extends Controller
{
    public function index()
    {
        $mots = Mot::all();
        return view('mots.index', compact('mots'));
    }

    public function create()
    {
        return view('mots.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mot_principal' => 'required|string|max:255',
            'mot_undercover' => 'required|string|max:255',
            'categorie' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('mots', 'public');
            $validated['image'] = $path;
        }

        Mot::create($validated);

        return redirect()->route('mots.index')->with('success', 'Mot créé avec succès.');
    }

    public function show(Mot $mot)
    {
        return view('mots.show', compact('mot'));
    }

    public function edit(Mot $mot)
    {
        return view('mots.edit', compact('mot'));
    }

    public function update(Request $request, Mot $mot)
    {
        $validated = $request->validate([
            'mot_principal' => 'required|string|max:255',
            'mot_undercover' => 'required|string|max:255',
            'categorie' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($mot->image) {
                Storage::disk('public')->delete($mot->image);
            }
            $path = $request->file('image')->store('mots', 'public');
            $validated['image'] = $path;
        }

        $mot->update($validated);

        return redirect()->route('mots.index')->with('success', 'Mot mis à jour avec succès.');
    }

    public function destroy(Mot $mot)
    {
        if ($mot->image) {
            Storage::disk('public')->delete($mot->image);
        }
        $mot->delete();

        return redirect()->route('mots.index')->with('success', 'Mot supprimé avec succès.');
    }
}
