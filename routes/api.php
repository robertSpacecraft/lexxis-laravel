<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\Catalog\ProductConfiguratorController;
use App\Http\Controllers\Api\Catalog\ProductController as CatalogProductController;
use App\Http\Controllers\Api\Catalog\ProductVariantController as CatalogProductVariantController;
use App\Http\Controllers\Api\PrintFileController as UserPrintFileController;
use App\Http\Controllers\Api\ProductDesignController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth (API / SPA)
Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/token-login', [AuthController::class, 'tokenLogin']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return ApiResponse::success($request->user());
    });
});

// Catálogo público (sin login)
Route::prefix('catalog')->group(function () {
    Route::get('/products', [CatalogProductController::class, 'index']);
    Route::get('/products/{product}', [CatalogProductController::class, 'show']);

    Route::get('/products/{product}/variants', [CatalogProductVariantController::class, 'index']);
    Route::get('/products/{product}/variants/{variant}', [CatalogProductVariantController::class, 'show']);

    Route::get('/products/{product}/configurator-options', [ProductConfiguratorController::class, 'options']);
});

// Zona autenticada
Route::middleware('auth:sanctum')->group(function () {
    // PrintFiles del usuario
    Route::get('/print-files', [UserPrintFileController::class, 'index']);
    Route::post('/print-files', [UserPrintFileController::class, 'store']);
    Route::get('/print-files/{printFile}', [UserPrintFileController::class, 'show']);
    Route::get('/print-files/{printFile}/download', [UserPrintFileController::class, 'download']);

    // ProductDesigns del usuario
    Route::prefix('product-designs')->group(function () {
        Route::get('/', [ProductDesignController::class, 'index']);
        Route::post('/', [ProductDesignController::class, 'store']);
        Route::get('/{productDesign}', [ProductDesignController::class, 'show']);
        Route::patch('/{productDesign}', [ProductDesignController::class, 'update']);
        Route::delete('/{productDesign}', [ProductDesignController::class, 'destroy']);
    });

    // Carrito del usuario
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'show']);
        Route::post('/product-variants/{variant}', [CartController::class, 'addProductVariant']);
        Route::post('/product-designs/{productDesign}', [CartController::class, 'addProductDesign']);
    });
});
