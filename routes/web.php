<?php

use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\productImageController;
use App\Http\Controllers\Admin\ProductVariantImageController;


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

        //CRUD de ProductImage
        Route::get('/products/{product}/images', [ProductImageController::class, 'index'])->name('products.images.index');
        Route::get('/products/{product}/images/create', [ProductImageController::class, 'create'])->name('products.images.create');
        Route::post('/products/{product}/images', [ProductImageController::class, 'store'])->name('products.images.store');
        Route::get('/products/{product}/images/{image}/edit', [ProductImageController::class, 'edit'])->name('products.images.edit');
        Route::put('products/{product}/images/{image}', [ProductImageController::class, 'update'])->name('products.images.update');
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');

        // CRUD de ProductVariantImages
        Route::get('/products/{product}/variants/{variant}/images', [ProductVariantImageController::class, 'index'])
            ->name('products.variants.images.index');
        Route::get('/products/{product}/variants/{variant}/images/create', [ProductVariantImageController::class, 'create'])
            ->name('products.variants.images.create');
        Route::post('/products/{product}/variants/{variant}/images', [ProductVariantImageController::class, 'store'])
            ->name('products.variants.images.store');
        Route::get('/products/{product}/variants/{variant}/images/{image}/edit', [ProductVariantImageController::class, 'edit'])
            ->name('products.variants.images.edit');
        Route::put('/products/{product}/variants/{variant}/images/{image}', [ProductVariantImageController::class, 'update'])
            ->name('products.variants.images.update');
        Route::delete('/products/{product}/variants/{variant}/images/{image}', [ProductVariantImageController::class, 'destroy'])
            ->name('products.variants.images.destroy');



    });

require __DIR__.'/auth.php';
