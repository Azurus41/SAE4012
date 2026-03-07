<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MotController;
use App\Http\Controllers\JoueurController;
use App\Http\Controllers\PartieController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthJoueurController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\OnlineGameController;

// --- Public Routes ---
Route::view('/', 'welcome')->name('welcome');

// Serve storage files directly without relying on symlinks
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    if (!in_array($folder, ['avatars', 'mots'])) {
        abort(404);
    }
    $path = storage_path('app/public/' . $folder . '/' . $filename);
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->where('filename', '.*');

// --- Joueur Auth Routes ---
Route::get('/joueur/login', [AuthJoueurController::class, 'showLoginForm'])->name('joueur_login');
Route::post('/joueur/login', [AuthJoueurController::class, 'login'])->name('joueur.login.submit');

Route::get('/joueur/register', [AuthJoueurController::class, 'showRegisterForm'])->name('joueur_register');
Route::post('/joueur/register', [AuthJoueurController::class, 'register'])->name('joueur.register.submit');

Route::post('/joueur/logout', [AuthJoueurController::class, 'logout'])->name('logout');

// Legacy redirects for GET requests
Route::get('/login', fn() => redirect()->route('joueur_login'))->name('login');
Route::get('/register', fn() => redirect()->route('joueur_register'))->name('register');

// --- Protected Joueur Routes ---
Route::middleware(['auth:joueur'])->group(function () {
    Route::get('/joueur/profile', [AuthJoueurController::class, 'profile'])->name('joueur.profile');
    Route::post('/joueur/profile', [AuthJoueurController::class, 'updateProfile'])->name('joueur.profile.update');
    Route::get('/joueur/leaderboard', [AuthJoueurController::class, 'leaderboard'])->name('joueur.leaderboard');
    Route::get('/joueur/parties', [AuthJoueurController::class, 'allParties'])->name('joueur.parties');
    Route::delete('/joueur/delete-account', [AuthJoueurController::class, 'deleteAccount'])->name('joueur.delete_account');

    // Online game
    Route::get('/game', [OnlineGameController::class, 'index'])->name('game.index');
    Route::post('/game/join', [OnlineGameController::class, 'join'])->name('game.join');
    Route::get('/game/{code}', [OnlineGameController::class, 'play'])->name('game.play');
    Route::get('/game/{code}/state', [OnlineGameController::class, 'getState'])->name('game.state');
    Route::get('/game/{code}/start-check', [OnlineGameController::class, 'startCheck'])->name('game.start_check');
    Route::post('/game/{code}/force-start', [OnlineGameController::class, 'forceStart'])->name('game.force_start');
    Route::post('/game/{code}/say', [OnlineGameController::class, 'sayWord'])->name('game.say');
    Route::post('/game/{code}/message', [OnlineGameController::class, 'sendMessage'])->name('game.message');
    Route::post('/game/{code}/vote', [OnlineGameController::class, 'vote'])->name('game.vote');
    Route::post('/game/{code}/guess', [OnlineGameController::class, 'guessMisterWhite'])->name('game.guess');
    Route::post('/game/{code}/leave', [OnlineGameController::class, 'leave'])->name('game.leave');
});

// --- Admin Auth Routes ---
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// --- Protected Admin Routes ---
Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('mots', MotController::class);
    Route::get('/joueurs/search', [JoueurController::class, 'search'])->name('joueurs.search');
    Route::resource('joueurs', JoueurController::class);
    Route::resource('parties', PartieController::class);
});
