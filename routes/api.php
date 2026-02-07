<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\Api\Catalog\ProductController as CatalogProductController;
use App\Http\Controllers\Api\Catalog\ProductVariantController as CatalogProductVariantController;

use App\Http\Controllers\Api\PrintFileController as UserPrintFileController;

// Auth (API / SPA)
Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/token-login', [AuthController::class, 'tokenLogin']);

});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me', function (Request $request) {
        return $request->user();
    });
});

// Catálogo público (sin login)
Route::prefix('catalog')->group(function () {
    Route::get('/products', [CatalogProductController::class, 'index']);
    Route::get('/products/{product}', [CatalogProductController::class, 'show']);

    Route::get('/products/{product}/variants', [CatalogProductVariantController::class, 'index']);
    Route::get('/products/{product}/variants/{variant}', [CatalogProductVariantController::class, 'show']);
});

// PrintFiles del usuario (con login)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/print-files', [UserPrintFileController::class, 'index']);
    Route::post('/print-files', [UserPrintFileController::class, 'store']);
    Route::get('/print-files/{printFile}', [UserPrintFileController::class, 'show']);
    Route::get('/print-files/{printFile}/download', [UserPrintFileController::class, 'download']); // útil para demo
});
