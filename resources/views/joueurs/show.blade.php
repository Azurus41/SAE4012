@extends('layouts.app')

@section('content')
<h1>Détails du Joueur: {{ $joueur->pseudo }}</h1>
<p>Email: {{ $joueur->email }}</p>
<p>Score: {{ $joueur->score_total }}</p>

<h2>Historique des Parties</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Rôle</th>
            <th>Points</th>
            <th>Mot</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        @foreach($joueur->parties as $partie)
        <tr>
            <td>#{{ $partie->id }}</td>
            <td>{{ $partie->date }}</td>
            <td>{{ $partie->pivot->role }}</td>
            <td>{{ $partie->pivot->points_gagnes }}</td>
            <td>{{ $partie->mot->mot_principal }}</td>
            <td>{{ $partie->statut }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<a href="{{ route('joueurs.index') }}" class="btn">Retour</a>
@endsection
