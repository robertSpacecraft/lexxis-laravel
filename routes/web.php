<?php

use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;

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

//Creo este grupo de rutas para el layout del admin protegida con el middleware configurado en /bootstrap/app.php || /Middleware/EnsureUserIsAdmin.php que usa las funciones de model/User.php
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        //CRUD de Products
        Route::resource('products', ProductController::class)
            ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        //CRUD de ProductsVariants
        Route::get('/products/{product}/variants', [ProductVariantController::class, 'variants'])->name('products.variants.index');
        Route::get('/products/{product}/variants/create', [ProductVariantController::class, 'variantsCreate'])
            ->name('products.variants.create');
        Route::post('/products/{product}/variants', [ProductVariantController::class, 'variantsStore'])
            ->name('products.variants.store');
        Route::get('/products/{product}/variants/{variant}/edit', [ProductVariantController::class, 'variantsEdit'])
            ->name('products.variants.edit');
        Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'variantsUpdate'])
            ->name('products.variants.update');
        Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'variantsDestroy'])
            ->name('products.variants.destroy');

        //CRUD de Materials
        Route::resource('materials', MaterialController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);
    });

require __DIR__.'/auth.php';
