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

    /**
     * Search for a joueur by pseudo and return statistics.
     */
    public function search(Request $request)
    {
        $q = $request->query('q');

        $joueur = null;
        $stats = null;

        if ($q) {
            $joueur = Joueur::where('pseudo', 'like', "%{$q}%")
                ->with(['parties' => function ($query) {
                    $query->withPivot('points_gagnes');
                }, 'partiesGagnees'])
                ->first();

            if ($joueur) {
                $totalParties = $joueur->parties->count();
                $victoires = $joueur->partiesGagnees->count();
                $totalPoints = $joueur->parties->sum(function ($p) {
                    return $p->pivot->points_gagnes ?? 0;
                });

                $moyennePoints = $totalParties > 0 ? round($totalPoints / $totalParties, 2) : 0;

                $stats = [
                    'total_parties' => $totalParties,
                    'victoires' => $victoires,
                    'total_points' => $totalPoints,
                    'moyenne_points' => $moyennePoints,
                ];
            }
        }

        return view('joueurs.search', compact('joueur', 'stats', 'q'));
    }

    public function create()
    {
        return view('joueurs.create');
    }

    public function store(Request $request)
    {
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
                'password' => [
                    'required',
                    'string',
                    'min:6'
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
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit faire au moins 6 caractères.',
                'score_total.required' => 'Le score est obligatoire.',
                'score_total.integer' => 'Le score doit être un nombre.',
                'score_total.min' => 'Le score ne peut pas être négatif.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.mimes' => 'Formats autorisés : jpeg, png, jpg.',
                'avatar.max' => 'L’image ne doit pas dépasser 2 Mo.',
            ]
        );

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
                'password' => [
                    'nullable',
                    'string',
                    'min:6'
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
                'password.min' => 'Le mot de passe doit faire au moins 6 caractères.',
                'score_total.required' => 'Le score est obligatoire.',
                'score_total.integer' => 'Le score doit être un nombre.',
                'score_total.min' => 'Le score ne peut pas être négatif.',
                'avatar.image' => 'Le fichier doit être une image.',
                'avatar.mimes' => 'Formats autorisés : jpeg, png, jpg.',
                'avatar.max' => 'L’image ne doit pas dépasser 2MB.',
            ]
        );

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
