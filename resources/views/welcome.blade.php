<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet Undercover - SAE 4012</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #1e293b;
            --accent: #10b981;
            --bg: #f8fafc;
            --white: #ffffff;
            --text: #1e293b;
            --text-light: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container {
            max-width: 850px;
            width: 100%;
            background: var(--white);
            padding: 3.5rem;
            border-radius: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .badge {
            display: inline-block;
            background: #dbeafe;
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-weight: 700;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--secondary);
            letter-spacing: -0.02em;
        }

        .project-desc {
            font-size: 1.125rem;
            line-height: 1.7;
            color: var(--text-light);
            margin-bottom: 2.5rem;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: var(--secondary);
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .footer {
            margin-top: 3rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .admin-link {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            color: var(--text-light);
            text-decoration: none;
            font-size: 0.875rem;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .admin-link:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Projet Undercover</h1>
        <p class="project-desc">
            Bienvenue sur notre plateforme d'Undercover en ligne, réalisé dans le cadre de la SAE 4012 par 3 étudiants de MMI2 groupe C. 
            L'objectif est de démontrer nos compétences dans le développement d'applications web avec le framework Laravel.
        </p>

        <div class="btn-group">
            <a href="{{ route('joueur_login') }}" class="btn btn-primary">Jouer maintenant</a>
            <a href="{{ route('joueur_register') }}" class="btn btn-secondary">Créer un compte</a>
        </div>

        <div class="footer">
            &copy; 2026 - Projet d'études - SAE 4012
        </div>
    </div>

    <a href="{{ route('admin.login') }}" class="admin-link">Administration</a>
</body>
</html>
