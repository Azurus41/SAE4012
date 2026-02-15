@extends('layouts.app')

@section('content')
<h1>{{ isset($mot) ? 'Modifier' : 'Ajouter' }} un Mot</h1>

<form action="{{ isset($mot) ? route('mots.update', $mot) : route('mots.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($mot))
        @method('PUT')
    @endif

    <div class="form-group">
        <label>Mot Principal</label>
        <input type="text" name="mot_principal" class="form-control" value="{{ $mot->mot_principal ?? '' }}" required>
    </div>

    <div class="form-group">
        <label>Mot Undercover</label>
        <input type="text" name="mot_undercover" class="form-control" value="{{ $mot->mot_undercover ?? '' }}" required>
    </div>

    <div class="form-group">
        <label>Catégorie</label>
        <input type="text" name="categorie" class="form-control" value="{{ $mot->categorie ?? '' }}" required>
    </div>

    <div class="form-group">
        <label>Image</label>
        <input type="file" name="image" class="form-control">
    </div>

    <button type="submit" class="btn">Enregistrer</button>
    <a href="{{ route('mots.index') }}" class="btn">Annuler</a>
</form>
@endsection
