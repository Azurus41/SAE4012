@extends('layouts.app')

@section('content')
<h1>Dashboard</h1>

<div class="grid">
    <div class="card">
        <h3>Mots</h3>
        <p>{{ $stats['mots_count'] }}</p>
    </div>
    <div class="card">
        <h3>Joueurs</h3>
        <p>{{ $stats['joueurs_count'] }}</p>
    </div>
    <div class="card">
        <h3>Parties</h3>
        <p>{{ $stats['parties_count'] }}</p>
    </div>
</div>

<h2>Dernières Parties</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Mot</th>
            <th>Gagnant</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stats['recent_parties'] as $partie)
        <tr>
            <td>#{{ $partie->id }}</td>
            <td>{{ $partie->date }}</td>
            <td>{{ $partie->mot->mot_principal }}</td>
            <td>{{ $partie->gagnant ? $partie->gagnant->pseudo : '-' }}</td>
            <td><span class="badge">{{ $partie->statut }}</span></td>
            <td>
                <a href="{{ route('parties.show', $partie) }}" class="btn">Voir</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
