<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MotController;
use App\Http\Controllers\JoueurController;
use App\Http\Controllers\PartieController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('mots', MotController::class);
Route::resource('joueurs', JoueurController::class);
Route::resource('parties', PartieController::class);
