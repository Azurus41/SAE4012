@extends('layouts.app')

@section('content')
<h1>Gestion des Joueurs</h1>
<a href="{{ route('joueurs.create') }}" class="btn">+ Ajouter</a>

<table>
    <thead>
        <tr>
            <th>Avatar</th>
            <th>Pseudo</th>
            <th>Email</th>
            <th>Score</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($joueurs as $joueur)
        <tr>
            <td>
                @if($joueur->avatar)
                    <img src="{{ asset('storage/' . $joueur->avatar) }}" class="thumbnail">
                @else
                    -
                @endif
            </td>
            <td>{{ $joueur->pseudo }}</td>
            <td>{{ $joueur->email }}</td>
            <td>{{ $joueur->score_total }}</td>
            <td>
                <a href="{{ route('joueurs.show', $joueur) }}" class="btn">Parties</a>
                <a href="{{ route('joueurs.edit', $joueur) }}" class="btn">Modifier</a>
                <form action="{{ route('joueurs.destroy', $joueur) }}" method="POST" style="display:inline;">
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
