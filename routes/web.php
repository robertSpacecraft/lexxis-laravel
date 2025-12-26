<?php

use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\productImageController;
use App\Http\Controllers\Admin\ProductVariantImageController;
use App\Http\Controllers\Admin\PrintFileController;
use App\Http\Controllers\Admin\PrintJobController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\OrderController;

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
        Route::get('/products/{product}/variants/{variant}', [ProductVariantController::class, 'variantsShow'])
            ->name('products.variants.show');

        // CRUD de PrintFiles
// CRUD de PrintFile
        Route::get('/print-files', [PrintFileController::class, 'index'])
            ->name('print-files.index');
        Route::get('/print-files/create', [PrintFileController::class, 'create'])
            ->name('print-files.create');
        Route::post('/print-files', [PrintFileController::class, 'store'])
            ->name('print-files.store');
        Route::get('/print-files/{printFile}/download', [PrintFileController::class, 'download'])
            ->name('print-files.download');
        Route::get('/print-files/{printFile}', [PrintFileController::class, 'show'])
            ->name('print-files.show');
        Route::get('/print-files/{printFile}/edit', [PrintFileController::class, 'edit'])
            ->name('print-files.edit');
        Route::put('/print-files/{printFile}', [PrintFileController::class, 'update'])
            ->name('print-files.update');
        Route::delete('/print-files/{printFile}', [PrintFileController::class, 'destroy'])
            ->name('print-files.destroy');

        // CRUD de PrintJobs (anidado bajo PrintFiles)
        Route::get('/print-files/{printFile}/jobs', [PrintJobController::class, 'index'])
            ->name('print-files.jobs.index');
        Route::get('/print-files/{printFile}/jobs/create', [PrintJobController::class, 'create'])
            ->name('print-files.jobs.create');
        Route::post('/print-files/{printFile}/jobs', [PrintJobController::class, 'store'])
            ->name('print-files.jobs.store');
        Route::get('/print-files/{printFile}/jobs/{printJob}', [PrintJobController::class, 'show'])
            ->name('print-files.jobs.show');
        Route::get('/print-files/{printFile}/jobs/{printJob}/edit', [PrintJobController::class, 'edit'])
            ->name('print-files.jobs.edit');
        Route::put('/print-files/{printFile}/jobs/{printJob}', [PrintJobController::class, 'update'])
            ->name('print-files.jobs.update');
        Route::delete('/print-files/{printFile}/jobs/{printJob}', [PrintJobController::class, 'destroy'])
            ->name('print-files.jobs.destroy');

        //CRUD de User
        Route::resource('users', UserController::class)
            ->only(['index', 'show', 'edit', 'update']);

        // CRUD de Addresses (anidado bajo Users)
        Route::get('/users/{user}/addresses', [AddressController::class, 'index'])
            ->name('users.addresses.index');
        Route::get('/users/{user}/addresses/{address}', [AddressController::class, 'show'])
            ->name('users.addresses.show');
        Route::get('/users/{user}/addresses/{address}/edit', [AddressController::class, 'edit'])
            ->name('users.addresses.edit');
        Route::put('/users/{user}/addresses/{address}', [AddressController::class, 'update'])
            ->name('users.addresses.update');
        Route::delete('/users/{user}/addresses/{address}', [AddressController::class, 'destroy'])
            ->name('users.addresses.destroy');

        //CRUD de Order
            //Solo lectura para el acceso desde el panel
        Route::get('/orders', [OrderController::class, 'globalIndex'])
            ->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'globalShow'])
            ->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])
            ->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])
            ->name('orders.update');

            //Resto de rutas anidadas a User
        Route::get('/users/{user}/orders', [OrderController::class, 'userIndex'])
            ->name('users.orders.index');
        Route::get('/users/{user}/orders/{order}', [OrderController::class, 'userShow'])
            ->name('users.orders.show');





    });

require __DIR__.'/auth.php';
