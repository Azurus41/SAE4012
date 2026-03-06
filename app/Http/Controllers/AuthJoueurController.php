<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Joueur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AuthJoueurController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.joueur_login');
    }

    public function showRegisterForm()
    {
        return view('auth.joueur_register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'pseudo' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:joueurs'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $joueur = Joueur::create([
            'pseudo' => $validated['pseudo'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'score_total' => 0,
        ]);

        Auth::guard('joueur')->login($joueur);

        return redirect()->route('joueur.profile');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('joueur')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('joueur/profile');
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('joueur')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function profile()
    {
        $id = Auth::guard('joueur')->id();
        $joueur = Joueur::with([
            'parties' => function($q) {
                $q->orderBy('date', 'desc');
            },
            'partiesGagnees'
        ])->findOrFail($id);

        return view('joueur.profile', compact('joueur'));
    }

    public function leaderboard()
    {
        $joueurs = Joueur::orderBy('score_total', 'desc')->get();
        return view('joueur.leaderboard', compact('joueurs'));
    }

    public function allParties()
    {
        $parties = \App\Models\Partie::with(['mot', 'gagnant'])->latest()->get();
        return view('joueur.parties', compact('parties'));
    }

    public function deleteAccount(Request $request)
    {
        $joueur = Auth::guard('joueur')->user();
        
        Auth::guard('joueur')->logout();
        
        $joueur->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Votre compte a été supprimé avec succès.');
    }

    public function updateProfile(Request $request)
    {
        $joueur = Auth::guard('joueur')->user();

        $validated = $request->validate([
            'pseudo' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_]{3,20}$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('joueurs')->ignore($joueur->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($joueur->avatar && Storage::disk('public')->exists($joueur->avatar)) {
                Storage::disk('public')->delete($joueur->avatar);
            }

            $filename = time() . '_' . $request->file('avatar')->getClientOriginalName();
            $path = $request->file('avatar')->storeAs('avatars', $filename, 'public');
            $joueur->avatar = $path;
        }

        $joueur->pseudo = $validated['pseudo'];
        $joueur->email = $validated['email'];
        $joueur->save();

        return redirect()->route('joueur.profile')->with('success', 'Profil mis à jour avec succès.');
    }
}
