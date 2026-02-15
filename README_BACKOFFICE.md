# Backoffice Laravel - Undercover Game Administration

## Introduction
Ce backoffice a été développé avec Laravel pour permettre à un administrateur de gérer les données d'un jeu type **Undercover**. 
L'interface remplace le concept classique d'e-commerce (Produits, Clients, Commandes) par des entités adaptées au jeu :
- **Mots (Produits)** : Les paires de mots (Civil/Undercover) utilisées pour les parties.
- **Joueurs (Clients)** : Les utilisateurs inscrits qui participent aux parties.
- **Parties (Commandes)** : Les sessions de jeu enregistrées, liant les mots et les joueurs.

## Structure des Entités et Relations

### 1. Mots (`mots`)
- `id`, `mot_principal` (Civil), `mot_undercover`, `categorie`, `image`.
- Relation : Un mot peut être utilisé dans plusieurs parties (**One-to-Many**).

### 2. Joueurs (`joueurs`)
- `id`, `pseudo`, `email`, `score_total`, `avatar`.
- Relation : Un joueur participe à plusieurs parties (**Many-to-Many**).

### 3. Parties (`parties`)
- `id`, `date`, `statut` (en attente, en cours, terminée), `mot_id`, `gagnant_id`.
- Relation Pivot (`joueur_partie`) : Table de liaison entre Joueurs et Parties contenant :
    - `role` (civil, undercover, mr_white)
    - `points_gagnes`
    - Timestamps

## Fonctionnalités Principales
- **CRUD Complet** : Gestion totale des Mots et des Joueurs.
- **Gestion des Parties** : Création de parties avec sélection dynamique des joueurs et attribution des rôles/points via une interface intuitive.
- **Consultation Croisée** :
    - Voir toutes les parties effectuées par un joueur spécifique (depuis sa fiche joueur).
    - Voir tous les joueurs participants à une partie spécifique et leurs statistiques.
- **Gestion des Images** : Upload d'images pour les mots et d'avatars pour les joueurs (stockage local via le disque `public`).
- **Dashboard** : Vue d'ensemble avec statistiques rapides et liste des dernières parties.

## Installation
1. Configurer le fichier `.env` avec vos identifiants MySQL.
2. Créer la base de données (ex: `undercover_db`).
3. Exécuter les migrations : `php artisan migrate`.
4. (Optionnel) Peupler avec des données de test : `php artisan db:seed --class=UndercoverSeeder`.
5. Lier le dossier de stockage : `php artisan storage:link`.
6. Lancer le serveur : `php artisan serve`.

## Design
L'interface utilise un design volontairement simple et minimaliste avec du CSS de base (fond blanc, texte noir, bordures simples) pour une lisibilité maximale et une légèreté d'exécution.
