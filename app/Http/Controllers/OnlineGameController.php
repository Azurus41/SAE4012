<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\OnlinePartie;
use App\Models\OnlinePartieJoueur;
use App\Models\OnlineMessage;
use App\Models\OnlineVote;
use App\Models\Mot;
use App\Models\Joueur;
use App\Models\Partie;
use App\Events\GameStateUpdated;

class OnlineGameController extends Controller
{
    // ─── Lobby & Matchmaking ───────────────────────────────────────────

    public function index()
    {
        $joueur = Auth::guard('joueur')->user();

        // Check if already in an active game
        $existing = OnlinePartieJoueur::where('joueur_id', $joueur->id)
            ->whereHas('partie', fn($q) => $q->whereIn('statut', ['waiting', 'playing', 'voting', 'mister_white_guess']))
            ->with('partie')
            ->first();

        if ($existing) {
            return redirect()->route('game.play', $existing->partie->code);
        }

        // ── Clean up expired online parties ──
        $this->cleanupOldParties();

        return view('joueur.game.index');
    }

    public function join(Request $request)
    {
        $joueur = Auth::guard('joueur')->user();

        // Check not already in a game
        $existing = OnlinePartieJoueur::where('joueur_id', $joueur->id)
            ->whereHas('partie', fn($q) => $q->whereIn('statut', ['waiting', 'playing', 'voting', 'mister_white_guess']))
            ->first();

        if ($existing) {
            return response()->json(['code' => $existing->partie->code]);
        }

        $this->cleanupOldParties();

        // Find a waiting lobby with room
        $partie = OnlinePartie::where('statut', 'waiting')
            ->withCount('joueurs')
            ->having('joueurs_count', '<', 12)
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();

        if (!$partie) {
            // Create a new one
            $partie = OnlinePartie::create([
                'code'   => strtoupper(Str::random(6)),
                'statut' => 'waiting',
            ]);
        }

        OnlinePartieJoueur::updateOrCreate([
            'online_partie_id' => $partie->id,
            'joueur_id'        => $joueur->id,
        ], [
            'ordre'            => $partie->joueurs()->count(),
        ]);

        $this->broadcastState($partie);

        return response()->json(['code' => $partie->code]);
    }

    public function startCheck(string $code)
    {
        $partie = OnlinePartie::where('code', $code)->with('joueurs.joueur')->firstOrFail();

        // Auto-start if 30s elapsed since first player joined
        if ($partie->statut === 'waiting') {
            $age = now()->diffInSeconds($partie->created_at);
            $count = $partie->joueurs()->count();

            if ($count >= 2 && $age >= 30) {
                $this->startGame($partie);
            }
        }

        return response()->json($this->buildState($partie, Auth::guard('joueur')->user()));
    }

    public function forceStart(string $code)
    {
        $partie = OnlinePartie::where('code', $code)->with('joueurs.joueur')->firstOrFail();
        if ($partie->statut === 'waiting' && $partie->joueurs()->count() >= 2) {
            $this->startGame($partie);
        }
        return response()->json(['ok' => true]);
    }

    // ─── Game View ─────────────────────────────────────────────────────

    public function play(string $code)
    {
        $joueur = Auth::guard('joueur')->user();
        $partie = OnlinePartie::where('code', $code)->firstOrFail();

        // Make sure the player is in this game
        $pj = OnlinePartieJoueur::where('online_partie_id', $partie->id)
            ->where('joueur_id', $joueur->id)
            ->first();

        if (!$pj) {
            return redirect()->route('game.index')->with('error', 'Vous n\'êtes pas dans cette partie.');
        }

        return view('joueur.game.play', compact('partie', 'joueur'));
    }

    // ─── Actions ───────────────────────────────────────────────────────

    public function sayWord(Request $request, string $code)
    {
        $joueur = Auth::guard('joueur')->user();
        $request->validate(['word' => 'required|string|max:100']);

        $partie = OnlinePartie::where('code', $code)->with('joueurs.joueur')->firstOrFail();
        if ($partie->statut !== 'playing') {
            return response()->json(['error' => 'Pas votre tour'], 400);
        }

        $actifs = $partie->joueursActifs()->get();
        $joueurActuelId = $actifs[$partie->joueur_actuel_index]->joueur_id ?? null;

        if ($joueurActuelId !== $joueur->id) {
            return response()->json(['error' => 'Ce n\'est pas votre tour'], 403);
        }

        // Record the word said
        $pj = OnlinePartieJoueur::where('online_partie_id', $partie->id)
            ->where('joueur_id', $joueur->id)
            ->first();
            
        if ($pj) {
            $pj->update([
                'a_parle' => true,
                'dernier_mot' => $request->word
            ]);

            $this->sendSystemMessage($partie, "{$joueur->pseudo} a écrit le mot : \"{$request->word}\"");
        }

        // Broadcast the word said (safe)
        $this->safeBroadcast($code, [
            'event'    => 'word_said',
            'joueur'   => $joueur->pseudo,
            'word'     => $request->word,
            'state'    => $this->buildState($partie, null),
        ]);

        // Matchmaking logic: Move to next player or start voting phase
        $this->advanceTurn($partie);

        $this->broadcastState($partie->fresh(['joueurs.joueur']));

        return response()->json($this->buildState($partie->fresh(['joueurs.joueur']), $joueur));
    }

    public function sendMessage(Request $request, string $code)
    {
        $joueur = Auth::guard('joueur')->user();
        $request->validate(['message' => 'required|string|max:300']);

        $partie = OnlinePartie::where('code', $code)->firstOrFail();

        // Only during voting/debate or after finish to say GG
        if (!in_array($partie->statut, ['voting', 'mister_white_guess', 'finished'])) {
            return response()->json(['error' => 'Chat pas disponible maintenant'], 400);
        }

        $pj = OnlinePartieJoueur::where('online_partie_id', $partie->id)
            ->where('joueur_id', $joueur->id)
            ->first();

        if (!$pj) {
            return response()->json(['error' => 'Joueur introuvable'], 404);
        }

        if ($pj->est_elimine && $partie->statut !== 'finished') {
            return response()->json(['error' => 'Vous êtes éliminé'], 403);
        }

        OnlineMessage::create([
            'online_partie_id' => $partie->id,
            'joueur_id'        => $joueur->id,
            'contenu'          => $request->message,
        ]);

        $this->safeBroadcast($code, [
            'event'   => 'new_message',
            'pseudo'  => $joueur->pseudo,
            'message' => $request->message,
        ]);

        return response()->json($this->buildState($partie, $joueur));
    }

    public function vote(Request $request, string $code)
    {
        $joueur = Auth::guard('joueur')->user();
        $request->validate(['cible_id' => 'required|integer']);

        $partie = OnlinePartie::where('code', $code)->with('joueurs.joueur')->firstOrFail();

        if ($partie->statut !== 'voting') {
            return response()->json(['error' => 'Pas en phase de vote'], 400);
        }

        $pj = OnlinePartieJoueur::where('online_partie_id', $partie->id)
            ->where('joueur_id', $joueur->id)->first();

        if (!$pj || $pj->est_elimine) {
            return response()->json(['error' => 'Vous êtes éliminé'], 403);
        }

        // Save or update vote
        OnlineVote::updateOrCreate(
            ['online_partie_id' => $partie->id, 'votant_id' => $joueur->id, 'tour_numero' => $partie->tour_numero],
            ['cible_id' => $request->cible_id]
        );

        $this->safeBroadcast($code, [
            'event'   => 'vote_cast',
            'state'   => $this->buildState($partie, null),
        ]);

        // Check if all active non-eliminated players have voted
        $actifs = $partie->joueursActifs()->get();
        $votes = OnlineVote::where('online_partie_id', $partie->id)->where('tour_numero', $partie->tour_numero)->count();

        if ($votes >= $actifs->count()) {
            $this->resolveVotes($partie);
            $partie = $partie->fresh(['joueurs.joueur']);
        }

        $this->broadcastState($partie);
        return response()->json($this->buildState($partie, $joueur));
    }

    public function guessMisterWhite(Request $request, string $code)
    {
        $joueur = Auth::guard('joueur')->user();
        $request->validate(['guess' => 'required|string|max:100']);

        $partie = OnlinePartie::where('code', $code)->with('joueurs.joueur')->firstOrFail();

        if ($partie->statut !== 'mister_white_guess') {
            return response()->json(['error' => 'Non'], 400);
        }

        $pj = OnlinePartieJoueur::where('online_partie_id', $partie->id)
            ->where('joueur_id', $joueur->id)
            ->where('role', 'mister_white')
            ->first();

        if (!$pj) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $correct = strtolower(trim($request->guess)) === strtolower(trim($partie->mot_civil));

        if ($correct) {
            // Mister white wins!
            $joueur->increment('score_total', 150);
            $this->endGame($partie, 'mister_white', [$joueur->id]);
        } else {
            // Mister white loses, check remaining win condition
            $this->checkWinCondition($partie);
        }

        return response()->json(['correct' => $correct]);
    }

    public function getState(string $code)
    {
        $joueur = Auth::guard('joueur')->user();
        $partie = OnlinePartie::where('code', $code)->with('joueurs.joueur')->firstOrFail();

        // Timer auto-actions - Secure single execution
        if ($partie->timer_expiry && now()->gt($partie->timer_expiry)) {
            // Tentative d'atomicité : on essaie de mettre le timer à null pour "prendre le verrou"
            $affected = OnlinePartie::where('id', $partie->id)
                ->whereNotNull('timer_expiry')
                ->where('timer_expiry', '<=', now())
                ->update(['timer_expiry' => null]);

            if ($affected > 0) {
                $this->handleTimerExpiry($partie);
                $partie = $partie->fresh(['joueurs.joueur']);
            }
        }

        return response()->json($this->buildState($partie, $joueur));
    }

    // ─── Private Game Logic ────────────────────────────────────────────

    private function startGame(OnlinePartie $partie): void
    {
        // Pick a random word
        $mot = Mot::inRandomOrder()->first();
        if (!$mot) return;

        // Shuffle players
        $joueurs = $partie->joueurs()->get()->shuffle();
        $count = $joueurs->count();

        // Assign roles: 1 undercover per 4 players, 1 mister white per 6 players if enough
        $nbUndercover = max(1, intdiv($count, 4));
        $nbMisterWhite = $count >= 6 ? 1 : 0;
        $nbCivil = $count - $nbUndercover - $nbMisterWhite;

        $roles = array_merge(
            array_fill(0, $nbCivil, 'civil'),
            array_fill(0, $nbUndercover, 'undercover'),
            array_fill(0, $nbMisterWhite, 'mister_white')
        );
        shuffle($roles);

        // Ensure mister white doesn't go first if present
        if ($nbMisterWhite > 0) {
            $mwIndex = array_search('mister_white', $roles);
            if ($mwIndex === 0) {
                // Swap with random non-first player
                $swapWith = rand(1, count($roles) - 1);
                [$roles[0], $roles[$swapWith]] = [$roles[$swapWith], $roles[0]];
            }
        }

        foreach ($joueurs as $i => $pj) {
            $role = $roles[$i];
            $word = match ($role) {
                'civil'        => $mot->mot_principal,
                'undercover'   => $mot->mot_undercover,
                'mister_white' => null,
            };

            $pj->update([
                'role'   => $role,
                'mot'    => $word,
                'ordre'  => $i,
                'a_parle' => false,
            ]);
        }

        $partie->update([
            'statut'            => 'playing',
            'mot_id'            => $mot->id,
            'mot_civil'         => $mot->mot_principal,
            'mot_undercover'    => $mot->mot_undercover,
            'joueur_actuel_index' => 0,
            'timer_expiry'      => now()->addSeconds(30),
            'tour_numero'       => 1,
        ]);

        $this->sendSystemMessage($partie, "La partie commence ! Tour 1.");
        
        $this->broadcastState($partie->fresh(['joueurs.joueur']));
    }

    private function resolveVotes(OnlinePartie $partie): void
    {
        $topVoted = OnlineVote::where('online_partie_id', $partie->id)
            ->where('tour_numero', $partie->tour_numero)
            ->selectRaw('cible_id, COUNT(*) as total')
            ->groupBy('cible_id')
            ->orderByDesc('total')
            ->get();

        if ($topVoted->isEmpty()) {
            $this->sendSystemMessage($partie, "Personne n'a voté. Nouveau tour !");
            $this->nextRound($partie);
            return;
        }

        $maxVotes = $topVoted->first()->total;
        $winners = $topVoted->where('total', $maxVotes);

        if ($winners->count() > 1) {
            $this->sendSystemMessage($partie, "Égalité ! Personne n'est éliminé ce tour-ci.");
            $this->nextRound($partie);
            return;
        }

        $votes = $winners->first();

        $eliminated = $partie->joueurs()->where('joueur_id', $votes->cible_id)->first();
            
        if (!$eliminated) {
            $this->nextRound($partie);
            return;
        }

        $eliminated->update(['est_elimine' => true]);
        $partie = $partie->fresh(['joueurs.joueur']); // Important for counts

        // Count votes
        $vCount = OnlineVote::where('online_partie_id', $partie->id)
            ->where('tour_numero', $partie->tour_numero)
            ->where('cible_id', $votes->cible_id)
            ->count();
            
        $this->sendSystemMessage($partie, "{$eliminated->joueur->pseudo} a été éliminé avec {$vCount} vote" . ($vCount > 1 ? 's' : '') . ". C'était un " . strtoupper($eliminated->role) . ".");

        $this->safeBroadcast($partie->code, [
            'event'      => 'player_eliminated',
            'pseudo'     => $eliminated->joueur->pseudo,
            'role'       => $eliminated->role,
            'state'      => $this->buildState($partie->fresh(['joueurs.joueur']), null),
        ]);

        // If eliminated is mister white → give them 30s to guess the word
        if ($eliminated->role === 'mister_white') {
            $partie->update([
                'statut'        => 'mister_white_guess',
                'timer_expiry'  => now()->addSeconds(30),
            ]);
            $this->broadcastState($partie->fresh(['joueurs.joueur']));
            return;
        }

        $this->checkWinCondition($partie->fresh(['joueurs.joueur']));
    }

    private function checkWinCondition(OnlinePartie $partie): void
    {
        $partie = $partie->fresh(['joueurs.joueur']);
        $actifs = $partie->joueursActifs()->get();

        $civils      = $actifs->where('role', 'civil')->count();
        $undercoverS = $actifs->where('role', 'undercover')->count();
        $misterWhite = $actifs->where('role', 'mister_white')->count();

        // Undercover wins if >= civilians
        if ($undercoverS > 0 && $undercoverS >= $civils) {
            $winners = $actifs->where('role', 'undercover')->pluck('joueur_id')->toArray();
            foreach ($winners as $jid) {
                \App\Models\Joueur::find($jid)?->increment('score_total', 125);
            }
            $this->endGame($partie, 'undercover', $winners);
            return;
        }

        // Civilians win if no undercover and no mister white
        if ($undercoverS === 0 && $misterWhite === 0) {
            $winners = $actifs->where('role', 'civil')->pluck('joueur_id')->toArray();
            foreach ($winners as $jid) {
                \App\Models\Joueur::find($jid)?->increment('score_total', 100);
            }
            $this->endGame($partie, 'civil', $winners);
            return;
        }

        // Game continues
        if ($partie->statut === 'playing') {
             // If we were in playing phase (AFK), we just need to advance turn
             $this->advanceTurn($partie);
        } else {
             // If we were in voting/guess phase, we advance to a new round
             $this->nextRound($partie);
        }
    }

    private function advanceTurn(OnlinePartie $partie): void
    {
        $actifs = $partie->joueursActifs()->get();
        $nextPj = $actifs->where('a_parle', false)->first();

        if ($nextPj) {
            $partie->update([
                'joueur_actuel_index' => $actifs->search(fn($item) => $item->id === $nextPj->id),
                'timer_expiry'        => now()->addSeconds(30),
            ]);
        } else {
            $partie->update([
                'statut'       => 'voting',
                'timer_expiry' => now()->addSeconds(120),
            ]);
            $this->sendSystemMessage($partie, "Tout le monde a parlé. La phase de débat et vote commence !");
        }
        $this->broadcastState($partie->fresh(['joueurs.joueur']));
    }

    private function nextRound(OnlinePartie $partie): void
    {
        // Reset a_parle and dernier_mot for remaining players
        OnlinePartieJoueur::where('online_partie_id', $partie->id)
            ->where('est_elimine', false)
            ->update([
                'a_parle' => false,
                'dernier_mot' => null
            ]);

        $newTour = $partie->tour_numero + 1;
        $partie->update([
            'statut'            => 'playing',
            'joueur_actuel_index' => 0,
            'timer_expiry'      => now()->addSeconds(30),
            'tour_numero'       => $newTour,
        ]);

        $this->sendSystemMessage($partie, "Nouveau tour ! Tour " . $newTour);

        $this->broadcastState($partie->fresh(['joueurs.joueur']));
    }

    private function endGame(OnlinePartie $onlinePartie, string $winner, array $winnerIds): void
    {
        $onlinePartie->update(['statut' => 'finished']);

        $label = match($winner) {
            'civil' => "Les CIVILS ont gagné !",
            'undercover' => "Les UNDERCOVERS ont gagné !",
            'mister_white' => "MISTER WHITE a gagné !",
            default => "Fin de partie."
        };
        $this->sendSystemMessage($onlinePartie, $label . " Le mot des civils était : \"{$onlinePartie->mot_civil}\"");

        // ── Convert to History (Admin \u0026 Parties Récentes) ──
        $historyPartie = Partie::create([
            'date'       => now(),
            'statut'     => 'finished',
            'mot_id'     => $onlinePartie->mot_id,
            'gagnant_id' => count($winnerIds) === 1 ? $winnerIds[0] : null // Pick first winner for simplified list
        ]);

        foreach ($onlinePartie->joueurs()->get() as $pj) {
            $isWinner = in_array($pj->joueur_id, $winnerIds);
            $points = 0;
            if ($isWinner) {
                $points = match($pj->role) {
                    'mister_white' => 150,
                    'undercover'   => 125,
                    'civil'        => 100,
                    default        => 0
                };
            }
            
            $historyPartie->joueurs()->attach($pj->joueur_id, [
                'role' => $pj->role,
                'points_gagnes' => $points
            ]);
        }

        $this->safeBroadcast($onlinePartie->code, [
            'event'      => 'game_over',
            'winner'     => $winner,
            'winnerIds'  => $winnerIds,
            'mot_civil'  => $onlinePartie->mot_civil,
            'state'      => $this->buildState($onlinePartie->fresh(['joueurs.joueur']), null),
        ]);
    }

    private function handleTimerExpiry(OnlinePartie $partie): void
    {
        if ($partie->statut === 'playing') {
            // Current player AFK → eliminate them
            $actifs = $partie->joueursActifs()->get();
            $pj = $actifs[$partie->joueur_actuel_index] ?? null;
            
            if ($pj && !$pj->a_parle) {
                $pj->update(['est_elimine' => true]);
                $this->safeBroadcast($partie->code, [
                    'event'  => 'afk_eliminated',
                    'pseudo' => $pj->joueur->pseudo,
                ]);
                // Check win conditions AFTER elimination
                $this->checkWinCondition($partie->fresh(['joueurs.joueur']));
            } else {
                // If they had spoken but somehow timer expired, just advance turn
                $this->advanceTurn($partie);
            }
        } elseif ($partie->statut === 'voting') {
            // Voting time over → tally votes
            $this->resolveVotes($partie);
        } elseif ($partie->statut === 'mister_white_guess') {
            // Mister white didn't guess in time
            $this->checkWinCondition($partie->fresh(['joueurs.joueur']));
        }
    }

    private function broadcastState(OnlinePartie $partie): void
    {
        try {
            broadcast(new GameStateUpdated($partie->code, [
                'event' => 'update',
                'state' => $this->buildState($partie, null),
            ]));
        } catch (\Exception $e) {
            // Ignore broadcasting errors, polling will fallback
        }
    }

    private function buildState(OnlinePartie $partie, $currentJoueur): array
    {
        $joueurs = $partie->joueurs()->with('joueur')->get()->map(function ($pj) use ($currentJoueur) {
            return [
                'id'          => $pj->joueur_id,
                'pseudo'      => $pj->joueur->pseudo,
                'est_elimine' => $pj->est_elimine,
                'a_parle'     => $pj->a_parle,
                'dernier_mot' => $pj->dernier_mot,
                'ordre'       => $pj->ordre,
                // Only reveal role/word to the player themselves
                'role'        => ($currentJoueur && $currentJoueur->id === $pj->joueur_id) ? $pj->role : null,
                'mot'         => ($currentJoueur && $currentJoueur->id === $pj->joueur_id) ? $pj->mot : null,
            ];
        });

        $messages = $partie->messages()->with('joueur')->get()->map(fn($m) => [
            'pseudo'   => $m->joueur ? $m->joueur->pseudo : 'Narrateur',
            'message'  => $m->contenu,
            'is_system'=> !$m->joueur_id,
            'time'     => $m->created_at->format('H:i'),
        ]);

        $votes = OnlineVote::where('online_partie_id', $partie->id)
            ->where('tour_numero', $partie->tour_numero)
            ->get()
            ->map(fn($v) => ['votant_id' => $v->votant_id, 'cible_id' => $v->cible_id]);

        return [
            'code'                => $partie->code,
            'statut'              => $partie->statut,
            'tour_numero'         => $partie->tour_numero,
            'joueur_actuel_index' => $partie->joueur_actuel_index,
            'timer_expiry'        => $partie->timer_expiry?->toISOString(),
            'joueurs'             => $joueurs,
            'messages'            => $messages,
            'votes'               => $votes,
            'mot_civil'           => $partie->statut === 'finished' ? $partie->mot_civil : null,
        ];
    }

    private function cleanupOldParties(): void
    {
        // 1. Delete finished games older than 10 mins
        OnlinePartie::where('statut', 'finished')
            ->where('updated_at', '<', now()->subMinutes(10))
            ->delete();

        // 2. Delete abandoned waiting/playing games older than 1 hour (emergency cleanup)
        OnlinePartie::where('statut', '!=', 'finished')
            ->where('updated_at', '<', now()->subHour())
            ->delete();
    }

    private function sendSystemMessage(OnlinePartie $partie, string $content): void
    {
        OnlineMessage::create([
            'online_partie_id' => $partie->id,
            'joueur_id'        => null,
            'contenu'          => $content,
        ]);

        $this->safeBroadcast($partie->code, [
            'event'     => 'new_message',
            'pseudo'    => 'Narrateur',
            'message'   => $content,
            'is_system' => true,
        ]);
    }

    private function safeBroadcast(string $code, array $payload): void
    {
        try {
            broadcast(new GameStateUpdated($code, $payload));
        } catch (\Exception $e) {
            // Silence broadcast errors on servers where Reverb/Pusher is down
            \Log::info("Broadcast failed: " . $e->getMessage());
        }
    }
}
