<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Joueur;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
<<<<<<< HEAD
        $validated = $request->validate([
            'pseudo' => 'required|string|max:255',
            'email' => 'required|email|unique:joueurs,email',
            'password' => 'required|string|min:6',
            'score_total' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
=======
        $validated = $request->validate(
            [
                'pseudo' => [
                    'required',
                    'regex:/^[A-Za-z0-9_]{3,20}$/'
                ],
                'email' => [
                    'required',
                    'email',
                    'unique:joueurs,email'
                ],
                'score_total' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:999999'
                ],
                'avatar' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,png,jpg',
                    'max:2048'
                ],
            ],
            [
                'pseudo.required' => 'Le pseudo est obligatoire.',
                'pseudo.regex' => 'Le pseudo doit contenir entre 3 et 20 caractères (lettres, chiffres ou underscore).',
                'email.required' => 'L’email est obligatoire.',
                'email.email' => 'L’email doit être valide.',
                'email.unique' => 'Cet email est déjà utilisé.',
                'score_total.required' => 'Le score est obligatoire.',
                'score_total.integer' => 'Le score doit être un nombre.',
                'score_total.min' => 'Le score ne peut pas être négatif.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.mimes' => 'Formats autorisés : jpeg, png, jpg.',
                'avatar.max' => 'L’image ne doit pas dépasser 2 Mo.',
            ]
        );
>>>>>>> 65d7151386e2a090a2014b2df65b63e7f8576075

        $validated['password'] = bcrypt($validated['password']);

        if ($request->hasFile('avatar')) {
            $filename = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');
            $validated['avatar'] = $path;
        }

        Joueur::create($validated);

        return redirect()->route('joueurs.index')
            ->with('success', 'Joueur créé avec succès.');
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
<<<<<<< HEAD
        $validated = $request->validate([
            'pseudo' => 'required|string|max:255',
            'email' => 'required|email|unique:joueurs,email,' . $joueur->id,
            'password' => 'nullable|string|min:6',
            'score_total' => 'nullable|integer',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
=======
        $validated = $request->validate(
            [
                'pseudo' => [
                    'required',
                    'regex:/^[A-Za-z0-9_]{3,20}$/'
                ],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('joueurs')->ignore($joueur->id),
                ],
                'score_total' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:999999'
                ],
                'avatar' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,png,jpg',
                    'max:2048'
                ],
            ],
            [
                'pseudo.required' => 'Le pseudo est obligatoire.',
                'pseudo.regex' => 'Le pseudo doit contenir entre 3 et 20 caractères (lettres, chiffres ou underscore).',
                'email.required' => 'L’email est obligatoire.',
                'email.email' => 'L’email doit être valide.',
                'email.unique' => 'Cet email est déjà utilisé.',
                'score_total.required' => 'Le score est obligatoire.',
                'score_total.integer' => 'Le score doit être un nombre.',
                'score_total.min' => 'Le score ne peut pas être négatif.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.mimes' => 'Formats autorisés : jpeg, png, jpg.',
                'avatar.max' => 'L’image ne doit pas dépasser 2MB.',
            ]
        );
>>>>>>> 65d7151386e2a090a2014b2df65b63e7f8576075

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('avatar')) {

            if ($joueur->avatar && Storage::disk('public')->exists($joueur->avatar)) {
                Storage::disk('public')->delete($joueur->avatar);
            }

            $filename = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');
            $validated['avatar'] = $path;
        }

        $joueur->update($validated);

        return redirect()->route('joueurs.index')
            ->with('success', 'Joueur mis à jour avec succès.');
    }

    public function destroy(Joueur $joueur)
    {
        if ($joueur->avatar && Storage::disk('public')->exists($joueur->avatar)) {
            Storage::disk('public')->delete($joueur->avatar);
        }

        $joueur->delete();

        return redirect()->route('joueurs.index')
            ->with('success', 'Joueur supprimé avec succès.');
    }
}
