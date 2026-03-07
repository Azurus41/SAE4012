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
            pattern="^[A-Za-z0-9_]{3,20}$"
            title="Le pseudo doit contenir entre 3 et 20 caractères (lettres, chiffres ou underscore uniquement).">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input 
            type="email" 
            name="email" 
            class="form-control" 
            value="{{ $joueur->email ?? '' }}" 
            required
            pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
            title="Veuillez entrer une adresse email valide.">
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
            pattern="^[0-9]+$"
            title="Le score doit être un nombre positif.">
    </div>

    <div class="form-group">
        <label>Avatar</label>
        @if(isset($joueur) && $joueur->avatar)
            <div style="margin-bottom: 10px;">
                <img src="{{ '/SAE4012/public/storage/' . $joueur->avatar }}" alt="Avatar" class="thumbnail" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
            </div>
        @endif
        <input 
            type="file" 
            name="avatar" 
            class="form-control"
            accept="image/png, image/jpeg, image/jpg"
            title="Formats autorisés : JPG, JPEG, PNG.">
    </div>

    <div class="form-group">
        <label>Mot de passe (laisser vide pour ne pas changer)</label>
        <input type="password" name="password" class="form-control">
    </div>

    <button type="submit" class="btn">Enregistrer</button>
    <a href="{{ route('joueurs.index') }}" class="btn">Annuler</a>
</form>
@endsection
