@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Recherche de Joueur</h1>

        <form method="GET" action="{{ route('joueurs.search') }}" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label for="q">Nom ou pseudo</label>
                <input id="q" name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Entrer le pseudo du joueur...">
            </div>
            <button class="btn">Rechercher</button>
        </form>

        @if(isset($joueur) && $joueur)
            <div class="card" style="padding:1rem;">
                <h2>{{ $joueur->pseudo }}</h2>
                <p>Email: {{ $joueur->email }}</p>
                <p>Score total: {{ $joueur->score_total }}</p>

                <h3>Statistiques</h3>
                <ul>
                    <li>Parties jouées: {{ $stats['total_parties'] ?? 0 }}</li>
                    <li>Victoires: {{ $stats['victoires'] ?? 0 }}</li>
                    <li>Points totaux gagnés: {{ $stats['total_points'] ?? 0 }}</li>
                    <li>Points moyen par partie: {{ $stats['moyenne_points'] ?? 0 }}</li>
                </ul>

                <a href="{{ route('joueurs.show', $joueur) }}" class="btn">Voir la fiche complète</a>
            </div>
        @elseif(isset($q) && $q)
            <p>Aucun joueur trouvé pour "{{ $q }}".</p>
        @endif

    </div>
@endsection
