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
        <input type="text" name="pseudo" class="form-control" value="{{ $joueur->pseudo ?? '' }}" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ $joueur->email ?? '' }}" required>
    </div>

    <div class="form-group">
        <label>Score Total</label>
        <input type="number" name="score_total" class="form-control" value="{{ $joueur->score_total ?? 0 }}">
    </div>

    <div class="form-group">
        <label>Avatar</label>
        <input type="file" name="avatar" class="form-control">
    </div>

    <button type="submit" class="btn">Enregistrer</button>
    <a href="{{ route('joueurs.index') }}" class="btn">Annuler</a>
</form>
@endsection
