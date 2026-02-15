@extends('layouts.app')

@section('content')
<h1>Gestion des Mots</h1>
<a href="{{ route('mots.create') }}" class="btn">+ Ajouter</a>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Mot Principal</th>
            <th>Mot Undercover</th>
            <th>Catégorie</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mots as $mot)
        <tr>
            <td>
                @if($mot->image)
                    <img src="{{ asset('storage/' . $mot->image) }}" class="thumbnail">
                @else
                    -
                @endif
            </td>
            <td>{{ $mot->mot_principal }}</td>
            <td>{{ $mot->mot_undercover }}</td>
            <td>{{ $mot->categorie }}</td>
            <td>
                <a href="{{ route('mots.edit', $mot) }}" class="btn">Modifier</a>
                <form action="{{ route('mots.destroy', $mot) }}" method="POST" style="display:inline;">
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
