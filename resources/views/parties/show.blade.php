@extends('layouts.app')

@section('content')
<h1>Partie #{{ $party->id }}</h1>
<p>Date: {{ $party->date }}</p>
<p>Statut: {{ $party->statut }}</p>
<p>Mot: {{ $party->mot->mot_principal }}</p>
<p>Gagnant: {{ $party->gagnant ? $party->gagnant->pseudo : '-' }}</p>

<h2>Joueurs</h2>
<table>
    <thead>
        <tr>
            <th>Joueur</th>
            <th>Rôle</th>
            <th>Points</th>
        </tr>
    </thead>
    <tbody>
        @foreach($party->joueurs as $joueur)
        <tr>
            <td>{{ $joueur->pseudo }}</td>
            <td>{{ $joueur->pivot->role }}</td>
            <td>{{ $joueur->pivot->points_gagnes }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<a href="{{ route('parties.index') }}" class="btn">Retour</a>
@endsection
