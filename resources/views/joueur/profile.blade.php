@extends('layouts.joueur')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Profil Joueur</h1>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 2rem;">
        <div class="card" style="text-align: center; display: flex; flex-direction: column; align-items: center; padding: 3rem 2rem;">
            <div style="width: 120px; height: 120px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 3.5rem; font-weight: 800; margin-bottom: 1.5rem; border: 1px solid var(--border); overflow: hidden;">
                @if($joueur->avatar)
                    <img src="{{ asset('storage/' . $joueur->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    {{ strtoupper(substr($joueur->pseudo, 0, 1)) }}
                @endif
            </div>
            <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $joueur->pseudo }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9375rem; margin-bottom: 2rem;">{{ $joueur->email }}</p>
            <div style="background: #f1f5f9; border: 1px solid var(--border); padding: 0.625rem 1.5rem; border-radius: 2rem; font-weight: 700; font-size: 0.875rem; color: var(--text-main);">
                Score Total : {{ $joueur->score_total }}
            </div>

            <div style="margin-top: 2rem; width: 100%; text-align: left;">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;">Modifier le profil</h3>
                
                @if ($errors->any())
                    <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem;">
                        <ul style="margin-left: 1.25rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('joueur.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom: 1rem;">
                        <label for="pseudo" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Pseudo</label>
                        <input type="text" name="pseudo" id="pseudo" value="{{ old('pseudo', $joueur->pseudo) }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem;" required>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="email" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $joueur->email) }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem;" required>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label for="avatar" style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">Avatar</label>
                        <input type="file" name="avatar" id="avatar" style="width: 100%; font-size: 0.875rem;">
                        <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">JPG, PNG max 2Mo</small>
                    </div>

                    <button type="submit" class="nav-item" style="width: 100%; background: var(--primary); color: white; border: none; cursor: pointer; justify-content: center;">
                        Mettre à jour
                    </button>
                </form>
            </div>
        </div>

        <div>
            <div class="card">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--text-main); border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
                    Statistiques de performance
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;">Parties Jouées</div>
                        <div style="font-size: 1.5rem; font-weight: 700;">{{ $joueur->parties->count() }}</div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;">Victoires</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent);">{{ $joueur->partiesGagnees->count() }}</div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;">Ratio Victoire</div>
                        <div style="font-size: 1.5rem; font-weight: 700;">
                            {{ $joueur->parties->count() > 0 ? round(($joueur->partiesGagnees->count() / $joueur->parties->count()) * 100) : 0 }}%
                        </div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem;">Points Moyens</div>
                        <div style="font-size: 1.5rem; font-weight: 700;">
                            {{ $joueur->parties->count() > 0 ? round($joueur->score_total / $joueur->parties->count()) : 0 }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--text-main); border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
                    Dernières activités
                </h3>
                @if($joueur->parties->count() > 0)
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; color: var(--text-muted); font-size: 0.8125rem; border-bottom: 1px solid var(--border);">
                                <th style="padding: 1rem 0;">Date</th>
                                <th style="padding: 1rem 0;">Rôle</th>
                                <th style="padding: 1rem 1rem;">Points</th>
                                <th style="padding: 1rem 0; text-align: right;">Résultat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($joueur->parties->take(5) as $partie)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 1rem 0; font-size: 0.875rem;">{{ $partie->date }}</td>
                                    <td style="padding: 1rem 0;"><span style="background: #f1f5f9; padding: 0.2rem 0.6rem; border-radius: 0.3rem; font-size: 0.75rem; font-weight: 600;">{{ $partie->pivot->role }}</span></td>
                                    <td style="padding: 1rem 1rem; font-weight: 600;">+{{ $partie->pivot->points_gagnes }}</td>
                                    <td style="padding: 1rem 0; text-align: right;">
                                        @if($partie->gagnant_id == $joueur->id)
                                            <span style="color: var(--accent); font-weight: 700; font-size: 0.8125rem;">VICTOIRE</span>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.8125rem;">DÉFAITE</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: var(--text-muted); font-style: italic;">Aucune partie encore jouée.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
