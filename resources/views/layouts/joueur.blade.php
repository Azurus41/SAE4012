<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Undercover Joueur' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #f8fafc;
            --sidebar-bg: #1e293b;
            --sidebar-text: #f8fafc;
            --sidebar-hover: #334155;
            --text-main: #111827;
            --text-muted: #6b7280;
            --primary: #2563eb;
            --primary-light: #dbeafe;
            --accent: #10b981;
            --white: #ffffff;
            --border: #e5e7eb;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 50;
        }

        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .sidebar-nav {
            padding: 1.5rem 1rem;
            flex-grow: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
            font-weight: 500;
        }

        .nav-item:hover {
            background-color: var(--sidebar-hover);
        }

        .nav-item.active {
            background-color: var(--primary);
            color: white;
        }

        .nav-item.nav-game {
            background-color: #065f46;
            color: #6ee7b7;
            margin-top: 0.5rem;
            font-weight: 700;
        }

        .nav-item.nav-game:hover {
            background-color: #047857;
            color: #a7f3d0;
        }

        /* Main Content */
        .main-wrapper {
            flex-grow: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            height: 64px;
            background-color: var(--white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .user-menu {
            position: relative;
            cursor: pointer;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }

        .user-trigger:hover {
            background-color: #f1f5f9;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background-color: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9375rem;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            background-color: var(--white);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            display: none;
            z-index: 100;
        }

        .user-menu:hover .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 0.625rem 0.75rem;
            color: var(--text-main);
            text-decoration: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: background-color 0.2s;
            border: none;
            width: 100%;
            text-align: left;
            background: none;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f8fafc;
        }

        .dropdown-item.danger {
            color: #ef4444;
        }

        .dropdown-item.danger:hover {
            background-color: #fef2f2;
        }

        .divider {
            height: 1px;
            background-color: var(--border);
            margin: 0.5rem 0;
        }

        /* Content Area */
        .content {
            padding: 2rem;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--text-main);
        }

        /* Utility Card */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h1>Undercover</h1>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('joueur.profile') }}" class="nav-item {{ request()->routeIs('joueur.profile') ? 'active' : '' }}">
                Tableau de bord
            </a>
            <a href="{{ route('joueur.leaderboard') }}" class="nav-item {{ request()->routeIs('joueur.leaderboard') ? 'active' : '' }}">
                Joueurs & Leaderboard
            </a>
            <a href="{{ route('joueur.parties') }}" class="nav-item {{ request()->routeIs('joueur.parties') ? 'active' : '' }}">
                Parties récentes
            </a>
            <a href="{{ route('game.index') }}" class="nav-item nav-game {{ request()->routeIs('game.*') ? 'active' : '' }}">
                ▶ Nouvelle Partie
            </a>
        </nav>
    </aside>

    <div class="main-wrapper">
        <header class="topbar">
            <div class="user-menu">
                <div class="user-trigger">
                    <span class="user-name">{{ Auth::guard('joueur')->user()->pseudo }}</span>
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::guard('joueur')->user()->pseudo, 0, 1)) }}
                    </div>
                </div>
                <div class="dropdown-menu">
                    <a href="{{ route('joueur.profile') }}" class="dropdown-item">Mon profil</a>
                    <div class="divider"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <button type="button" onclick="confirmLogout()" class="dropdown-item">Déconnexion</button>
                    
                    <form id="delete-account-form" action="{{ route('joueur.delete_account') }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" onclick="confirmDelete()" class="dropdown-item danger">Supprimer mon compte</button>
                </div>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div style="padding: 1rem; background-color: #ecfdf5; border: 1px solid #10b981; color: #10b981; border-radius: 0.5rem; margin-bottom: 2rem;">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function confirmLogout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                document.getElementById('logout-form').submit();
            }
        }

        function confirmDelete() {
            if (confirm('CETTE ACTION EST IRRÉVERSIBLE. Êtes-vous certain de vouloir supprimer votre compte définitivement ?')) {
                if (confirm('Vraiment certain ? Toutes vos données seront effacées.')) {
                    document.getElementById('delete-account-form').submit();
                }
            }
        }
    </script>
</body>
</html>
