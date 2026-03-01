@extends('layouts.joueur')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Nouvelle Partie</h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Rejoignez automatiquement une partie en ligne avec d'autres joueurs.</p>
    </div>

    <div class="card" style="max-width: 480px; text-align: center; padding: 3rem 2.5rem;">
        <div style="font-size: 3.5rem; margin-bottom: 1.5rem;">🕵️</div>
        <h2 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem;">Undercover en ligne</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem; line-height: 1.7;">
            Cliquez pour rejoindre le matchmaking. La partie démarrera automatiquement après 30 secondes ou dès que 2 joueurs seront prêts.
        </p>

        @if(session('error'))
            <div style="background: #fef2f2; color: #ef4444; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
        @endif

        <button id="join-btn" onclick="joinQueue()" style="display: inline-flex; align-items: center; gap: 0.75rem; background: var(--primary); color: white; border: none; padding: 1rem 2.5rem; border-radius: 0.75rem; font-size: 1.125rem; font-weight: 700; cursor: pointer; transition: all 0.2s;">
            <span>Rejoindre une partie</span>
        </button>

        <div id="status" style="display:none; margin-top: 1.5rem; color: var(--text-muted);">
            <div style="display: flex; align-items: center; justify-content: center; gap: 0.75rem;">
                <div class="spinner"></div>
                <span id="status-text">Recherche d'une partie en cours...</span>
            </div>
        </div>
    </div>

    <div class="card" style="max-width: 480px; margin-top: 1.5rem; padding: 1.5rem 2rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Règles du jeu</h3>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.75rem; color: var(--text-main); font-size: 0.9375rem;">
            <li>👥 De 2 à 12 joueurs par partie</li>
            <li>🃏 Chaque joueur reçoit un mot secret (ou pas de mot si Mister White)</li>
            <li>🗣️ Chacun dit un mot à tour de rôle (30s)</li>
            <li>💬 Débat de 120s puis vote pour éliminer un joueur</li>
            <li>🎯 Mister White peut gagner en devinant le mot des civils</li>
            <li>🏆 Civils: 100pts | Undercovs: 125pts | M.White: 150pts</li>
        </ul>
    </div>

    <style>
        .spinner {
            width: 20px; height: 20px;
            border: 3px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <form id="join-form" action="{{ route('game.join') }}" method="POST" style="display:none;">
        @csrf
    </form>

    <script>
        function joinQueue() {
            document.getElementById('join-btn').disabled = true;
            document.getElementById('join-btn').style.opacity = '0.6';
            document.getElementById('status').style.display = 'block';
            
            fetch('{{ route('game.join') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            })
            .then(async r => {
                if (!r.ok) {
                    const txt = await r.text();
                    console.error("Matchmaking error:", txt);
                    throw new Error("Server error");
                }
                return r.json();
            })
            .then(data => {
                if (data.code) {
                    document.getElementById('status-text').textContent = 'Partie trouvée ! Redirection...';
                    window.location.href = "{{ url('/game') }}/" + data.code;
                } else {
                    throw new Error("No code in response");
                }
            })
            .catch((err) => {
                console.error(err);
                document.getElementById('status-text').textContent = 'Erreur de connexion. Réessayez.';
                document.getElementById('join-btn').disabled = false;
                document.getElementById('join-btn').style.opacity = '1';
            });
        }
    </script>
@endsection
