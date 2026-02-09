<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

// Página principal - Galeria de imagens (público)
Route::get('/', [VoteController::class, 'index'])->name('gallery');

// Processar voto (público)
Route::post('/vote', [VoteController::class, 'vote'])->name('vote');

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação Admin
|--------------------------------------------------------------------------
*/

// Login
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rotas Admin (Protegidas por autenticação)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {
    // Logout
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    
    // Dashboard - Página de upload
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Upload de imagem
    Route::post('/upload', [ImageController::class, 'upload'])->name('admin.upload');
    
    // Gestão de imagens
    Route::get('/manage', [ImageController::class, 'manage'])->name('admin.manage');
    
    // Ver votos de uma imagem
    Route::get('/images/{id}/votes', [ImageController::class, 'viewVotes'])->name('admin.images.votes');
    
    // Remover imagem
    Route::delete('/images/{id}', [ImageController::class, 'delete'])->name('admin.images.delete');
    
    // Estatísticas
    Route::get('/statistics', [ImageController::class, 'statistics'])->name('admin.statistics');
});
