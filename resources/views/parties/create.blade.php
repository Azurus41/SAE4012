@extends('layouts.app')

@section('content')
<h1>{{ isset($party) ? 'Modifier' : 'Créer' }} une Partie</h1>

<form action="{{ isset($party) ? route('parties.update', $party) : route('parties.store') }}" method="POST">
    @csrf
    @if(isset($party))
        @method('PUT')
    @endif

    <div class="card">
        <h3>Infos</h3>
        <div class="form-group">
            <label>Date</label>
            <input type="datetime-local" name="date" class="form-control" value="{{ isset($party) ? date('Y-m-d\TH:i', strtotime($party->date)) : date('Y-m-d\TH:i') }}" required>
        </div>
        <div class="form-group">
            <label>Statut</label>
            <select name="statut" class="form-control">
                <option value="en_attente" {{ ($party->statut ?? '') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                <option value="en_cours" {{ ($party->statut ?? '') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="terminee" {{ ($party->statut ?? '') == 'terminee' ? 'selected' : '' }}>Terminée</option>
            </select>
        </div>
        <div class="form-group">
            <label>Mot</label>
            <select name="mot_id" class="form-control">
                @foreach($mots as $mot)
                    <option value="{{ $mot->id }}" {{ ($party->mot_id ?? '') == $mot->id ? 'selected' : '' }}>{{ $mot->mot_principal }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Gagnant</label>
            <select name="gagnant_id" class="form-control">
                <option value="">-</option>
                @foreach($joueurs as $joueur)
                    <option value="{{ $joueur->id }}" {{ ($party->gagnant_id ?? '') == $joueur->id ? 'selected' : '' }}>{{ $joueur->pseudo }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card">
        <h3>Joueurs</h3>
        <div id="players-list">
            @if(isset($party))
                @foreach($party->joueurs as $participant)
                    <div style="margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
                        Joueur:
                        <select name="joueurs[]">
                            @foreach($joueurs as $joueur)
                                <option value="{{ $joueur->id }}" {{ $participant->id == $joueur->id ? 'selected' : '' }}>{{ $joueur->pseudo }}</option>
                            @endforeach
                        </select>
                        Rôle:
                        <select name="roles[]">
                            <option value="civil" {{ $participant->pivot->role == 'civil' ? 'selected' : '' }}>Civil</option>
                            <option value="undercover" {{ $participant->pivot->role == 'undercover' ? 'selected' : '' }}>Undercover</option>
                            <option value="mr_white" {{ $participant->pivot->role == 'mr_white' ? 'selected' : '' }}>Mr. White</option>
                        </select>
                        Points:
                        <input type="number" name="points[]" value="{{ $participant->pivot->points_gagnes }}">
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn" onclick="addPlayer()">+ Ajouter un joueur</button>
    </div>

    <button type="submit" class="btn">Enregistrer</button>
    <a href="{{ route('parties.index') }}" class="btn">Annuler</a>
</form>

<template id="player-row">
    <div style="margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
        Joueur:
        <select name="joueurs[]">
            @foreach($joueurs as $joueur)
                <option value="{{ $joueur->id }}">{{ $joueur->pseudo }}</option>
            @endforeach
        </select>
        Rôle:
        <select name="roles[]">
            <option value="civil">Civil</option>
            <option value="undercover">Undercover</option>
            <option value="mr_white">Mr. White</option>
        </select>
        Points:
        <input type="number" name="points[]" value="0">
    </div>
</template>

<script>
    function addPlayer() {
        const list = document.getElementById('players-list');
        const template = document.getElementById('player-row');
        list.appendChild(template.content.cloneNode(true));
    }
    @if(!isset($party))
        addPlayer();
    @endif
</script>
@endsection
