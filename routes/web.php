<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//Creo esta ruta protegida con el middleware configurado en /bootstrap/app.php || /Middleware/EnsureUserIsAdmin.php que usa las funciones de model/User.php
Route::middleware(['auth', 'admin'])
    ->get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })
    ->name('admin.dashboard');

require __DIR__.'/auth.php';
