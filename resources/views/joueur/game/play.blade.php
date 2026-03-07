<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partie #{{ $partie->code }} - Undercover</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --border: #334155;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --primary: #3b82f6;
            --green: #10b981;
            --red: #ef4444;
            --yellow: #f59e0b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background: var(--bg); color: var(--text); min-height: 100vh; }

        .game-layout {
            display: grid;
            grid-template-columns: 280px 1fr 280px;
            grid-template-rows: auto 1fr;
            height: 100vh;
            max-height: 100vh;
            max-width: 1400px;
            margin: 0 auto;
            gap: 1px;
            overflow: hidden;
        }

        .players-panel, .game-center, .chat-panel {
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ── Header ── */
        .game-header {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--card);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
        }
        .game-title { font-weight: 800; font-size: 1.25rem; }
        .game-code { background: var(--border); padding: 0.375rem 0.875rem; border-radius: 0.5rem; font-size: 0.875rem; letter-spacing: 0.1em; }

        /* ── Timer ── */
        .timer-bar {
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        .timer-fill {
            height: 100%;
            background: var(--primary);
            transition: width 1s linear;
            border-radius: 2px;
        }
        .timer-label { font-size: 0.75rem; color: var(--muted); margin-top: 0.25rem; }

        /* ── Players panel ── */
        .players-panel {
            background: var(--card);
            border-right: 1px solid var(--border);
            padding: 1.5rem 1rem;
            overflow-y: auto;
        }
        .panel-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border);
        }
        .player-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.375rem;
            transition: background 0.15s;
        }
        .player-item.active { background: rgba(59, 130, 246, 0.15); border-left: 3px solid var(--primary); }
        .player-item.eliminated { opacity: 0.4; text-decoration: line-through; }
        .player-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--border);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem;
            flex-shrink: 0;
        }
        .player-name { font-weight: 600; font-size: 0.9375rem; }
        .word-badge {
            background: rgba(59, 130, 246, 0.2);
            color: var(--primary);
            padding: 0.375rem 1rem;
            border-radius: 2rem;
            font-weight: 700;
            font-size: 0.9375rem;
            margin-top: 1rem;
            display: inline-block;
        }
        .role-badge {
            font-size: 0.7rem;
            color: var(--muted);
            margin-top: 0.125rem;
        }

        /* ── Center game area ── */
        .game-center {
            display: flex;
            flex-direction: column;
            background: var(--bg);
        }
        .game-status {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
            gap: 1.5rem;
        }
        .status-label {
            font-size: 0.875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }
        .status-main {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1.2;
        }
        .action-btn {
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1.125rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #2563eb; transform: translateY(-2px); }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .btn-green { background: var(--green); color: white; }
        .btn-red { background: var(--red); color: white; }
        .btn-yellow { background: var(--yellow); color: #111; }

        input.word-input {
            background: var(--card);
            border: 2px solid var(--border);
            color: var(--text);
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            outline: none;
            width: 100%;
            max-width: 400px;
            letter-spacing: 0.05em;
            transition: border-color 0.2s;
        }
        input.word-input:focus { border-color: var(--primary); }

        /* ── Vote list ── */
        .vote-card {
            background: var(--card);
            border: 2px solid var(--border);
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            max-width: 400px;
            color: var(--text);
            font-size: 1rem;
            font-weight: 600;
        }
        .vote-card:hover { border-color: var(--primary); background: rgba(59,130,246,0.1); }
        .vote-card.selected { border-color: var(--red); background: rgba(239,68,68,0.1); }

        /* ── Chat ── */
        .chat-panel {
            background: var(--card);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .chat-msg { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .chat-msg .from { font-size: 0.75rem; font-weight: 700; color: var(--muted); margin-bottom: 0.125rem; }
        .chat-msg .text {
            background: var(--border);
            padding: 0.5rem 0.875rem;
            border-radius: 0 0.75rem 0.75rem 0.75rem;
            font-size: 0.9375rem;
            word-break: break-word;
            display: inline-block;
            max-width: 100%;
        }
        .chat-msg.mine .text {
            background: rgba(59,130,246,0.25);
            border-radius: 0.75rem 0 0.75rem 0.75rem;
        }
        .chat-input-row {
            display: flex;
            gap: 0.5rem;
            padding: 1rem;
            border-top: 1px solid var(--border);
            align-items: center;
            background: var(--card);
        }
        .chat-input {
            flex: 1;
            min-width: 0;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            outline: none;
        }
        .chat-send {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-weight: 700;
            cursor: pointer;
        }

        /* ── Game Over  ── */
        .gameover-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.85);
            display: flex; align-items: center; justify-content: center;
            z-index: 999;
        }
        .gameover-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            padding: 3rem;
            text-align: center;
            max-width: 480px;
            width: 90%;
        }
        .gameover-emoji { font-size: 4rem; margin-bottom: 1rem; }
        .gameover-title { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; }
        .gameover-sub { color: var(--muted); margin-bottom: 2rem; }

        /* Spinner */
        .spinner { width: 24px; height: 24px; border: 3px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Words already said */
        .word-said-row { display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid var(--border); }
        .word-said-player { font-weight: 600; }
        .word-said-word { color: var(--primary); font-style: italic; font-size: 0.875rem; }
    </style>
</head>
<body>
<div class="game-layout" id="app">
    <!-- Header -->
    <header class="game-header">
        <span class="game-title">🕵️ Undercover en ligne</span>
        <div style="display:flex; align-items:center; gap: 1.5rem;">
            <div id="timer-container" style="display:none; min-width: 150px;">
                <div class="timer-bar"><div class="timer-fill" id="timer-fill"></div></div>
                <div class="timer-label" id="timer-label">—</div>
            </div>
            <span class="game-code" id="game-code">{{ $partie->code }}</span>
            <form action="{{ route('game.leave', $partie->code) }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="action-btn btn-red" style="padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 0.5rem;" onclick="return confirm('Quitter la partie ?')">Quitter</button>
            </form>
        </div>
        <span style="color: var(--muted); font-size: 0.875rem;">{{ $joueur->pseudo }}</span>
    </header>

    <!-- Players -->
    <aside class="players-panel">
        <div class="panel-title">Joueurs</div>
        <div id="players-list"></div>
        <div id="my-word-area" style="padding: 0.5rem; display:none;">
            <div style="font-size:0.75rem; font-weight:700; color: var(--muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">Votre mot</div>
            <span class="word-badge" id="my-word">—</span>
            <div class="role-badge" id="my-role" style="display:none;">—</div>
        </div>
    </aside>

    <!-- Center -->
    <main class="game-center">
        <div class="game-status" id="game-status">
            <div>
                <div class="spinner"></div>
                <p style="color: var(--muted); margin-top: 1rem;">Connexion en cours...</p>
            </div>
        </div>
    </main>

    <!-- Chat -->
    <aside class="chat-panel">
        <div class="panel-title" style="padding: 1rem 1rem 0.5rem; border-bottom: 1px solid var(--border);">Chat</div>
        <div class="chat-messages" id="chat-messages">
            <p style="color: var(--muted); font-size: 0.8125rem; text-align: center; margin-top: 1rem;">Le chat est disponible lors du débat.</p>
        </div>
        <div class="chat-input-row" id="chat-input-row" style="display:none;">
            <input class="chat-input" id="chat-input" placeholder="Votre message..." maxlength="300" />
            <button class="chat-send" onclick="sendMessage()">→</button>
        </div>
    </aside>
</div>

<!-- Game over overlay -->
<div class="gameover-overlay" id="gameover-overlay" style="display:none;">
    <div class="gameover-card">
        <div class="gameover-emoji" id="go-emoji">🏆</div>
        <div class="gameover-title" id="go-title">Fin de partie</div>
        <div class="gameover-sub" id="go-sub"></div>
        <div id="go-word" style="margin-bottom:1.5rem; font-size:0.9375rem; color: var(--muted);"></div>
        <a href="{{ route('game.index') }}" class="action-btn btn-primary" style="display: inline-block; text-decoration: none;">Rejouer</a>
        <button class="action-btn" onclick="hideGameOver()" style="background:transparent; border:1px solid var(--border); color:var(--text); margin-top: 1rem; width: 100%;">Rester et discuter</button>
    </div>
</div>

<script>
const BASE_URL = '{{ url("/game") }}';
const GAME_CODE = '{{ $partie->code }}';
const MY_ID = {{ $joueur->id }};
const MY_PSEUDO = '{{ addslashes($joueur->pseudo) }}';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

let lastStatut = null;
let lastActuelIndex = null;
let lastTour = null;
let lastMsgCount = 0;
let lastVoteCount = 0;
let timerInterval = null;
let lastTimerExpiry = null;
let overlayDismissed = false;

// ── Polling fallback (Reverb optional) ──────────────────────────────────

let pollInterval = setInterval(fetchState, 1500);

async function fetchState() {
    try {
        const r = await fetch(`${BASE_URL}/${GAME_CODE}/state`, { headers: { Accept: 'application/json' }});
        const data = await r.json();
        renderState(data);
    } catch(e) { console.error(e); }
}

fetchState();

// ── Render ───────────────────────────────────────────────────────────────

function renderState(data) {
    if (!data) return;
    state = data;

    renderPlayers(data);
    renderCenter(data);
    renderTimer(data);

    // Show chat during voting, mister white guess, or when finished
    const chatActive = ['voting', 'mister_white_guess', 'finished'].includes(data.statut);
    document.getElementById('chat-input-row').style.display = chatActive ? 'flex' : 'none';

    if (data.statut === 'finished') {
        // Continue polling to receive GG messages
    }
}

function renderPlayers(data) {
    const list = document.getElementById('players-list');
    const actifIndex = data.joueur_actuel_index;
    const actifs = data.joueurs.filter(j => !j.est_elimine);

    list.innerHTML = data.joueurs.map((j, i) => {
        const isActive = data.statut === 'playing' && actifs.indexOf(j) !== -1 && actifs.indexOf(j) === actifIndex;
        const avatarContent = j.avatar 
            ? `<img src="/SAE4012/public/storage/${j.avatar}" style="width:100%;height:100%;object-fit:cover;">` 
            : j.pseudo[0].toUpperCase();
            
        return `<div class="player-item ${j.est_elimine ? 'eliminated' : ''} ${isActive ? 'active' : ''}">
            <div class="player-avatar" style="overflow:hidden;">${avatarContent}</div>
            <div>
                <div class="player-name">${j.pseudo}${j.id == MY_ID ? ' <span style="font-size:0.65rem;color:var(--primary);">(Vous)</span>' : ''}</div>
                ${j.est_elimine ? '<div style="font-size:0.7rem;color:var(--red);">Éliminé</div>' : ''}
            </div>
        </div>`;
    }).join('');

    // Show my word if game started
    const me = data.joueurs.find(j => j.id == MY_ID);
    if (me && me.mot !== null && me.mot !== undefined) {
        document.getElementById('my-word-area').style.display = 'block';
        document.getElementById('my-word').textContent = me.mot || '(Aucun mot)';
        const roleLabel = { civil: '🧑 Civil', undercover: '🤫 Undercover', mister_white: '🤍 Mister White' };
        document.getElementById('my-role').textContent = roleLabel[me.role] || '';
    } else {
        document.getElementById('my-word-area').style.display = 'none';
    }
}

function renderTimer(data) {
    if (!data.timer_expiry) {
        document.getElementById('timer-container').style.display = 'none';
        return;
    }
    document.getElementById('timer-container').style.display = 'block';

    if (lastTimerExpiry !== data.timer_expiry) {
        lastTimerExpiry = data.timer_expiry;
        if (timerInterval) clearInterval(timerInterval);
        startTimer(new Date(data.timer_expiry), getTimerMax(data.statut));
    }
}

function getTimerMax(statut) {
    switch(statut) {
        case 'playing': return 30;
        case 'voting': return 120;
        case 'mister_white_guess': return 30;
        default: return 30;
    }
}

function startTimer(expiry, maxSecs) {
    timerInterval = setInterval(() => {
        const remaining = Math.max(0, Math.round((expiry - new Date()) / 1000));
        const pct = (remaining / maxSecs) * 100;
        const fill = document.getElementById('timer-fill');
        const label = document.getElementById('timer-label');
        if (fill) fill.style.width = pct + '%';
        if (fill) fill.style.background = remaining <= 10 ? '#ef4444' : '#3b82f6';
        if (label) label.textContent = remaining + 's';
        if (remaining <= 0) clearInterval(timerInterval);
    }, 500);
}

function renderCenter(data) {
    const center = document.getElementById('game-status');
    const me = data.joueurs.find(j => j.id == MY_ID);
    const isEliminated = me?.est_elimine;

    // Check if we really need to update the center area
    const currentInput = document.activeElement;
    const isTyping = currentInput && (currentInput.id === 'word-input' || currentInput.id === 'mw-guess' || currentInput.id === 'chat-input');
    
    // Update words anyway if we are in playing phase
    if (data.statut === 'playing') {
        const wordsContainer = document.getElementById('words-said-list');
        if (wordsContainer) {
            const html = data.joueurs
                .filter(j => j.dernier_mot)
                .map(j => `<div class="word-said-row"><span class="word-said-player">${j.pseudo}</span><span class="word-said-word">"${escapeHtml(j.dernier_mot)}"</span></div>`)
                .join('');
            if (wordsContainer.innerHTML !== html) wordsContainer.innerHTML = html;
        }
    }

    // Only skip FULL redraw if the status and turn are the same and we are typing
    if (isTyping && lastStatut === data.statut && lastActuelIndex === data.joueur_actuel_index && lastTour === data.tour_numero) {
        return;
    }

    lastStatut = data.statut;
    lastActuelIndex = data.joueur_actuel_index;
    lastTour = data.tour_numero;

    switch(data.statut) {
        case 'waiting':
            renderWaiting(center, data);
            break;
        case 'playing':
            renderPlaying(center, data, isEliminated);
            break;
        case 'voting':
            renderVoting(center, data, isEliminated);
            break;
        case 'mister_white_guess':
            renderMisterWhiteGuess(center, data, me);
            break;
        case 'finished':
            renderFinished(data);
            break;
    }
}

function renderWaiting(center, data) {
    const count = data.joueurs.length;
    const canStart = count >= 2;
    center.innerHTML = `
        <div class="status-label">Salle d'attente</div>
        <div class="status-main">${count} / 12 joueur${count>1?'s':''}</div>
        <p style="color:var(--muted); max-width:320px;">La partie démarrera automatiquement dans 30s ou dès que vous la lancez.</p>
        ${canStart ? `<button class="action-btn btn-primary" onclick="forceStart()">Lancer la partie maintenant</button>` : '<div class="spinner"></div>'}
        <p style="color:var(--muted); font-size:0.8125rem;">En attente d'autres joueurs...</p>
    `;
}

function renderPlaying(center, data, isEliminated) {
    if (isEliminated) {
        center.innerHTML = `<div class="status-label">Vous êtes éliminé</div><div class="status-main" style="font-size:3rem;">💀</div><p style="color:var(--muted);">Observez la suite de la partie.</p>`;
        return;
    }

    const actifs = data.joueurs.filter(j => !j.est_elimine);
    const currentPlayer = actifs[data.joueur_actuel_index];
    const isMyTurn = currentPlayer && currentPlayer.id == MY_ID;

    // Build words said from players list
    const wordsSaidHtml = data.joueurs
        .filter(j => j.dernier_mot)
        .map(j => `<div class="word-said-row"><span class="word-said-player">${j.pseudo}</span><span class="word-said-word">"${escapeHtml(j.dernier_mot)}"</span></div>`)
        .join('');

    center.innerHTML = `
        <div class="status-label">Tour ${data.tour_numero} — C'est au tour de</div>
        <div class="status-main">${currentPlayer ? currentPlayer.pseudo : '—'}</div>
        <div id="words-said-list" style="width:100%;max-width:450px; text-align:left;">${wordsSaidHtml}</div>
        ${isMyTurn ? `
            <p style="color:var(--muted);">Dites un mot en lien avec votre mot secret (30s)</p>
            <input class="word-input" id="word-input" placeholder="Votre mot..." maxlength="50" onkeydown="if(event.key==='Enter') submitWord()" autofocus />
            <button class="action-btn btn-primary" onclick="submitWord()">Valider</button>
        ` : `<p style="color:var(--muted);">Attendez votre tour...</p>`}
    `;
}

function renderVoting(center, data, isEliminated) {
    const actifs = data.joueurs.filter(j => !j.est_elimine);
    const myVote = data.votes.find(v => v.votant_id == MY_ID);
    const alreadyVoted = !!myVote;

    // Count remaining vote time
    const votePhaseStart = state?.timer_expiry;
    const now = new Date();
    const votingOpen = votePhaseStart && (new Date(votePhaseStart) - now) < 15000;

    center.innerHTML = `
        <div class="status-label">Phase de débat & vote</div>
        <div class="status-main" style="font-size:1.5rem;">Vote pour éliminer un joueur</div>
        <div style="display:flex; flex-direction:column; gap:0.5rem; width:100%; max-width:400px; align-items:center;">
        ${actifs.filter(j => j.id != MY_ID).map(j => {
            const voteCount = data.votes.filter(v => v.cible_id == j.id).length;
            const isSelected = myVote?.cible_id == j.id;
            return `<button class="vote-card ${isSelected ? 'selected' : ''}" onclick="castVote(${j.id})" ${alreadyVoted || isEliminated ? 'disabled style="opacity:0.6;cursor:not-allowed;"' : ''}>
                <div class="player-avatar" style="width:28px;height:28px;font-size:0.7rem;">${j.pseudo[0].toUpperCase()}</div>
                <span>${j.pseudo}</span>
                <span style="margin-left:auto;font-size:0.75rem;color:var(--muted);">${voteCount} vote${voteCount>1?'s':''}</span>
            </button>`;
        }).join('')}
        </div>
        ${alreadyVoted ? '<p style="color:var(--green);font-size:0.875rem;">✓ Vote enregistré</p>' : ''}
        <p style="color:var(--muted); font-size:0.8125rem;">Les votes seront comptabilisés automatiquement.</p>
    `;
}

function renderMisterWhiteGuess(center, data, me) {
    const isMW = me?.role === 'mister_white';

    if (isMW) {
        center.innerHTML = `
            <div class="status-label">Mister White — Votre chance !</div>
            <div class="status-main" style="font-size:1.75rem;">Devinez le mot des civils</div>
            <p style="color:var(--muted);">Si vous devinez juste, vous gagnez 150 points !</p>
            <input class="word-input" id="mw-guess" placeholder="Le mot secret est..." maxlength="100" onkeydown="if(event.key==='Enter') submitGuess()" autofocus />
            <button class="action-btn btn-yellow" onclick="submitGuess()">Deviner !</button>
        `;
    } else {
        center.innerHTML = `
            <div class="status-label">Mister White a été éliminé</div>
            <div class="status-main" style="font-size:3rem;">🤍</div>
            <p style="color:var(--muted);">Mister White tente de deviner le mot secret des civils...</p>
        `;
    }
}

function renderFinished(data) {
    if (overlayDismissed) return;
    // Show overlay
    const overlay = document.getElementById('gameover-overlay');
    overlay.style.display = 'flex';

    // Data comes from game_over event or state
    const winnerLabel = { civil: 'Les Civils gagnent !', undercover: 'Les Undercoverss gagnent !', mister_white: 'Mister White gagne !' };
    const winnerEmoji = { civil: '🏅', undercover: '🤫', mister_white: '🤍' };
    
    // We don't know the winner here unless stored
    document.getElementById('go-title').textContent = 'Partie terminée !';
    document.getElementById('go-sub').textContent = 'Consultez le classement pour voir vos points.';
    if (data.mot_civil) {
        document.getElementById('go-word').textContent = `Le mot des civils était : "${data.mot_civil}"`;
    }
}

function hideGameOver() {
    overlayDismissed = true;
    document.getElementById('gameover-overlay').style.display = 'none';
}

// ── Actions ──────────────────────────────────────────────────────────────

async function forceStart() {
    await fetch(`${BASE_URL}/${GAME_CODE}/force-start`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    });
    fetchState();
}

async function submitWord() {
    const input = document.getElementById('word-input');
    if (!input || !input.value.trim()) return;
    const word = input.value.trim();

    input.disabled = true;
    // wordsSaid[MY_ID] removed, we wait for state update

    const r = await fetch(`${BASE_URL}/${GAME_CODE}/say`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ word })
    });
    const data = await r.json();
    renderState(data);
}

async function castVote(cibleId) {
    const r = await fetch(`${BASE_URL}/${GAME_CODE}/vote`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ cible_id: cibleId })
    });
    const data = await r.json();
    renderState(data);
}

async function submitGuess() {
    const input = document.getElementById('mw-guess');
    if (!input || !input.value.trim()) return;

    const r = await fetch(`${BASE_URL}/${GAME_CODE}/guess`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ guess: input.value.trim() })
    });
    const data = await r.json();


    if (data.correct) {
        document.getElementById('game-status').innerHTML = `<div class="status-main" style="color:var(--green);">✓ Bonne réponse ! Vous gagnez 150 points !</div>`;
    } else {
        document.getElementById('game-status').innerHTML = `<div class="status-main" style="color:var(--red);">✗ Mauvaise réponse...</div>`;
    }
    setTimeout(fetchState, 1500);
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    if (!input.value.trim()) return;
    
    const msg = input.value.trim();
    input.value = '';

    await fetch(`${BASE_URL}/${GAME_CODE}/message`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ message: msg })
    });
}

document.getElementById('chat-input')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') sendMessage();
});

let lastMessageCount = 0;
function checkNewMessages(data) {
    if (!data.messages) return;
    if (data.messages.length > lastMessageCount) {
        const newMsgs = data.messages.slice(lastMessageCount);
        newMsgs.forEach(m => addChatMessage(m.pseudo, m.message, m.pseudo === MY_PSEUDO, m.is_system));
        lastMessageCount = data.messages.length;
    }
}

function addChatMessage(pseudo, text, isMine, isSystem = false) {
    const container = document.getElementById('chat-messages');
    const msg = document.createElement('div');
    if (isSystem) {
        msg.className = `chat-msg system`;
        msg.style.textAlign = 'center';
        msg.style.margin = '0.5rem 0';
        msg.innerHTML = `<div class="text" style="font-style: italic; background:transparent; color: var(--muted); border:none; padding: 0.25rem 0; font-size: 0.8125rem;">${escapeHtml(text)}</div>`;
    } else {
        msg.className = `chat-msg ${isMine ? 'mine' : ''}`;
        msg.innerHTML = `<div class="from">${pseudo}</div><div class="text">${escapeHtml(text)}</div>`;
    }
    container.appendChild(msg);
    container.scrollTop = container.scrollHeight;
}

function escapeHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Override renderState to also handle messages
const _origRender = renderState;
renderState = function(data) {
    _origRender(data);
    checkNewMessages(data);
};

// ── Reverb / WebSocket  ──────────────────────────────────────────────────
// If Reverb is configured, subscribe to the channel for instant updates
if (window.Echo) {
    window.Echo.channel(`game.${GAME_CODE}`)
        .listen('.state', (e) => {
            if (e.event === 'game_over') {
                const overlay = document.getElementById('gameover-overlay');
                overlay.style.display = 'flex';
                const labels = { civil: 'Les Civils gagnent !', undercover: 'Les Undercoverss gagnent !', mister_white: 'Mister White gagne !' };
                document.getElementById('go-title').textContent = labels[e.winner] || 'Fin de partie';
                document.getElementById('go-emoji').textContent = { civil: '🏅', undercover: '🤫', mister_white: '🤍' }[e.winner] || '🏆';
                if (e.mot_civil) document.getElementById('go-word').textContent = `Le mot des civils était : "${e.mot_civil}"`;
            }
            if (e.state) renderState(e.state);
        });
}
</script>
</body>
</html>
