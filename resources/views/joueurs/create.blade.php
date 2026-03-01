@extends('layouts.app')

@section('content')
<h1>{{ isset($joueur) ? 'Modifier' : 'Ajouter' }} un Joueur</h1>

<form action="{{ isset($joueur) ? route('joueurs.update', $joueur) : route('joueurs.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($joueur))
        @method('PUT')
    @endif

    <div class="form-group">
        <label>Pseudo</label>
        <input 
            type="text"
            name="pseudo"
            class="form-control"
            value="{{ $joueur->pseudo ?? '' }}"
            required
            pattern="^[A-Za-z0-9_-]{3,20}$"
            title="3 à 20 caractères : lettres, chiffres, tiret ou underscore uniquement"
        >
    </div>

    <div class="form-group">
        <label>Email</label>
        <input
            type="email"
            name="email"
            class="form-control"
            value="{{ $joueur->email ?? '' }}"
            required
            pattern="^[^\s@]+@[^\s@]+\.[^\s@]{2,}$"
            title="Veuillez entrer une adresse email valide"
        >
    </div>

    <div class="form-group">
        <label>Score Total</label>
        <input
            type="number"
            name="score_total"
            class="form-control"
            value="{{ $joueur->score_total ?? 0 }}"
            min="0"
            max="999999"
            pattern="^\d{1,6}$"
            title="Nombre entre 0 et 999999"
        >
    </div>

    <div class="form-group">
        <label>Avatar</label>
        <input
            type="file"
            name="avatar"
            class="form-control"
            accept="image/png, image/jpeg, image/jpg"
        >
        <small>Formats autorisés : JPG, JPEG, PNG</small>
    </div>

    <div class="form-group">
        <label>Mot de passe {{ isset($joueur) ? '(laisser vide pour ne pas changer)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ isset($joueur) ? '' : 'required' }}>
    </div>

    <button type="submit" class="btn">Enregistrer</button>
    <a href="{{ route('joueurs.index') }}" class="btn">Annuler</a>
</form>
@endsection
