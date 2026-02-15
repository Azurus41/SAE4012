<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Undercover Admin' }}</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #333333;
            display: flex;
        }

        aside {
            width: 200px;
            background-color: #f0f0f0;
            border-right: 1px solid #cccccc;
            padding: 20px;
            height: 100vh;
            position: fixed;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul li {
            margin-bottom: 10px;
        }

        nav ul li a {
            text-decoration: none;
            color: #333333;
            font-weight: bold;
        }

        nav ul li a.active {
            color: #0000ff;
            text-decoration: underline;
        }

        main {
            margin-left: 240px;
            padding: 20px;
            flex-grow: 1;
        }

        h1, h2, h3 {
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #cccccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #eeeeee;
        }

        .btn {
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #333333;
            background-color: #ffffff;
            color: #333333;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #dddddd;
        }

        .btn-danger {
            color: #ff0000;
        }

        .card {
            border: 1px solid #cccccc;
            padding: 15px;
            margin-bottom: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #cccccc;
        }

        .alert {
            padding: 10px;
            border: 1px solid #00aa00;
            background-color: #eeffee;
            color: #00aa00;
            margin-bottom: 20px;
        }

        .badge {
            padding: 2px 5px;
            border: 1px solid #666666;
            font-size: 12px;
            border-radius: 3px;
        }

        .thumbnail {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 1px solid #cccccc;
        }
    </style>
</head>
<body>
    <aside>
        <h3>Admin</h3>
        <nav>
            <ul>
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('mots.index') }}" class="{{ request()->routeIs('mots.*') ? 'active' : '' }}">Mots</a></li>
                <li><a href="{{ route('joueurs.index') }}" class="{{ request()->routeIs('joueurs.*') ? 'active' : '' }}">Joueurs</a></li>
                <li><a href="{{ route('parties.index') }}" class="{{ request()->routeIs('parties.*') ? 'active' : '' }}">Parties</a></li>
            </ul>
        </nav>
    </aside>

    <main>
        @if(session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
