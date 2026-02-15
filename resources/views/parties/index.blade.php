@extends('layouts.app')

@section('content')
<h1>Gestion des Parties</h1>
<a href="{{ route('parties.create') }}" class="btn">+ Ajouter</a>

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
        @foreach($parties as $partie)
        <tr>
            <td>#{{ $partie->id }}</td>
            <td>{{ $partie->date }}</td>
            <td>{{ $partie->mot->mot_principal }}</td>
            <td>{{ $partie->gagnant ? $partie->gagnant->pseudo : '-' }}</td>
            <td>{{ $partie->statut }}</td>
            <td>
                <a href="{{ route('parties.show', $partie) }}" class="btn">Détails</a>
                <a href="{{ route('parties.edit', $partie) }}" class="btn">Modifier</a>
                <form action="{{ route('parties.destroy', $partie) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
