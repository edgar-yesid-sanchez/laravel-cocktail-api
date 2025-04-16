<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CocktailController;
use App\Http\Controllers\FavoriteCocktailsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home/{letter?}', [CocktailController::class, 'create'])->name('home');
    Route::get('/api/cocktails/{letter}', [CocktailController::class, 'fetchAjax'])->name('cocktails.fetch');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/favorite-cocktails', [FavoriteCocktailsController::class, 'create'])->name('favorites.index');
    Route::post('/favorite-cocktails', [FavoriteCocktailsController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{cocktail}', [FavoriteCocktailsController::class, 'destroy'])->name('favorites.destroy');
    Route::put('/favorites/{cocktail}', [FavoriteCocktailsController::class, 'update'])->name('favorites.update');
});
require __DIR__.'/auth.php';
